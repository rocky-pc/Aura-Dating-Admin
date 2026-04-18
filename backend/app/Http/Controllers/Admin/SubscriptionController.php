<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * List all subscriptions with filters
     */
    public function index(Request $request)
    {
        $query = Subscription::with('user')->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        if ($request->has('plan')) {
            $query->where('plan', $request->plan);
        }

        $subscriptions = $query->paginate($request->get('per_page', 20));

        return response()->json($subscriptions);
    }

    /**
     * Web - List all subscriptions with filters
     */
    public function webIndex(Request $request)
    {
        $query = Subscription::with('user')->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('plan') && $request->plan) {
            $query->where('plan', $request->plan);
        }

        $subscriptions = $query->paginate(20);

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('is_active', true)->count(),
            'gold' => Subscription::where('plan', 'gold')->count(),
            'platinum' => Subscription::where('plan', 'platinum')->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    /**
     * Get single subscription details
     */
    public function show(Subscription $subscription)
    {
        $subscription->load('user');

        return response()->json($subscription);
    }

    /**
     * Create subscription for user (manual admin assignment)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan' => 'required|in:gold,platinum,premium_plus',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'auto_renew' => 'sometimes|boolean',
        ]);

        // Check if user already has active subscription
        $existingActive = Subscription::where('user_id', $validated['user_id'])
            ->where('is_active', true)
            ->first();

        if ($existingActive) {
            // Deactivate existing subscription
            $existingActive->update(['is_active' => false]);
        }

        $subscription = Subscription::create([
            'user_id' => $validated['user_id'],
            'plan' => $validated['plan'],
            'starts_at' => $validated['starts_at'],
            'expires_at' => $validated['expires_at'],
            'is_active' => true,
            'auto_renew' => $validated['auto_renew'] ?? false,
            'stripe_subscription_id' => 'admin_' . uniqid(),
            'stripe_customer_id' => 'admin_' . uniqid(),
        ]);

        // Update user premium status
        User::where('id', $validated['user_id'])->update([
            'is_premium' => true,
            'premium_expires_at' => $validated['expires_at'],
        ]);

        return response()->json([
            'message' => 'Subscription created successfully',
            'subscription' => $subscription->load('user'),
        ], 201);
    }

    /**
     * Cancel subscription
     */
    public function cancel(Subscription $subscription)
    {
        $subscription->update([
            'is_active' => false,
            'auto_renew' => false,
        ]);

        // Update user premium status
        $subscription->user->update(['is_premium' => false]);

        return redirect()->back()->with('success', 'Subscription cancelled successfully');
    }

    /**
     * Extend subscription
     */
    public function extend(Request $request, Subscription $subscription)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $newExpiry = $subscription->expires_at->addDays($request->days);

        $subscription->update([
            'expires_at' => $newExpiry,
        ]);

        // Update user premium status
        $subscription->user->update([
            'premium_expires_at' => $newExpiry,
        ]);

        return response()->json([
            'message' => 'Subscription extended successfully',
            'subscription' => $subscription->fresh('user'),
        ]);
    }

    /**
     * Get subscription statistics
     */
    public function stats()
    {
        $stats = [
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('is_active', true)->count(),
            'expired_subscriptions' => Subscription::where('is_active', false)->count(),
            'auto_renew_enabled' => Subscription::where('auto_renew', true)->count(),
            'by_plan' => Subscription::select('plan', DB::raw('count(*) as count'))
                ->groupBy('plan')
                ->get(),
            'revenue_this_month' => Subscription::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'new_subscriptions_today' => Subscription::whereDate('created_at', today())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get available plans
     */
    public function plans()
    {
        return response()->json([
            'plans' => [
                'gold' => [
                    'name' => 'Gold',
                    'price' => 9.99,
                    'features' => ['unlimited_likes', 'see_who_likes', 'unlimited_rewinds'],
                ],
                'platinum' => [
                    'name' => 'Platinum',
                    'price' => 14.99,
                    'features' => ['all_gold_features', 'boosts', 'priority_likes'],
                ],
                'premium_plus' => [
                    'name' => 'Premium Plus',
                    'price' => 24.99,
                    'features' => ['all_platinum_features', 'unlimited_boosts', 'see_all_likes'],
                ],
            ],
        ]);
    }
}

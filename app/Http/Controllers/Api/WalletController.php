<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletSettings;
use App\Models\UserSwipe;
use App\Models\UserMatch;
use App\Models\Message;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Get current user's wallet with detailed information
     */
    public function show(Request $request)
    {
        $user = $request->user();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'bonus_points' => 0,
                'total_spent' => 0,
                'lifetime_earnings' => 0,
            ]
        );

        // Get wallet settings for costs
        $settings = WalletSettings::all()->pluck('value', 'key');

        // Get usage statistics
        $stats = [
            'total_swipes' => UserSwipe::where('swiper_id', $user->id)->count(),
            'total_likes' => UserSwipe::where('swiper_id', $user->id)->whereIn('action', ['like', 'super_like'])->count(),
            'total_super_likes' => UserSwipe::where('swiper_id', $user->id)->where('action', 'super_like')->count(),
            'total_matches' => UserMatch::where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
            })->where('is_active', true)->count(),
            'total_messages_sent' => Message::where('sender_id', $user->id)->count(),
        ];

        // Recent activities (last 10)
        $recentActivities = collect();

        // Recent swipes
        $recentSwipes = UserSwipe::with('swiped:id,email')
            ->where('swiper_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($swipe) {
                return [
                    'type' => 'swipe',
                    'action' => $swipe->action,
                    'target_user' => $swipe->swiped->email,
                    'cost' => $swipe->action === 'super_like' ? 5 : 0,
                    'created_at' => $swipe->created_at->toISOString(),
                ];
            });

        // Recent matches
        $recentMatches = UserMatch::with(['userOne:id,email', 'userTwo:id,email'])
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($match) use ($user) {
                $otherUser = $match->user_one_id === $user->id ? $match->userTwo : $match->userOne;
                return [
                    'type' => 'match',
                    'target_user' => $otherUser->email,
                    'created_at' => $match->created_at->toISOString(),
                ];
            });

        // Combine and sort recent activities
        $recentActivities = $recentSwipes->concat($recentMatches)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        // Points breakdown
        $pointsBreakdown = [
            'balance_points' => $wallet->balance,
            'bonus_points' => $wallet->bonus_points,
            'total_available' => $wallet->total_points,
            'total_spent' => $wallet->total_spent,
            'lifetime_earnings' => $wallet->lifetime_earnings,
        ];

        // Feature costs
        $featureCosts = [
            'super_like' => $settings->get('super_like_cost', 5),
            'boost' => $settings->get('boost_cost', 20),
            'message' => $settings->get('message_cost', 0),
            'premium_monthly' => $settings->get('premium_monthly_cost', 999),
        ];

        // Calculate potential savings or recommendations
        $insights = [
            'super_likes_used_this_week' => UserSwipe::where('swiper_id', $user->id)
                ->where('action', 'super_like')
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'can_afford_super_like' => $wallet->total_points >= $featureCosts['super_like'],
            'can_afford_boost' => $wallet->total_points >= $featureCosts['boost'],
            'points_needed_for_premium' => max(0, $featureCosts['premium_monthly'] - $wallet->total_points),
        ];

        return response()->json([
            'wallet' => $wallet,
            'total_points' => $wallet->total_points,
            'points_breakdown' => $pointsBreakdown,
            'feature_costs' => $featureCosts,
            'usage_stats' => $stats,
            'recent_activities' => $recentActivities,
            'insights' => $insights,
            'settings' => $settings,
        ]);
    }

    /**
     * Get wallet settings (for displaying costs)
     */
    public function settings()
    {
        $settings = WalletSettings::all()->pluck('value', 'key');
        
        return response()->json([
            'settings' => $settings,
        ]);
    }

    /**
     * Add points to wallet (admin or purchase)
     */
    public function addPoints(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'type' => 'sometimes|in:balance,bonus',
        ]);

        $user = $request->user();
        
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'bonus_points' => 0,
                'total_spent' => 0,
                'lifetime_earnings' => 0,
            ]
        );

        $type = $request->get('type', 'bonus');
        
        if ($type === 'bonus') {
            $wallet->increment('bonus_points', $request->points);
        } else {
            $wallet->increment('balance', $request->points);
        }

        return response()->json([
            'message' => 'Points added successfully',
            'wallet' => $wallet->fresh(),
            'total_points' => $wallet->total_points,
        ]);
    }

    /**
     * Deduct points from wallet
     */
    public function deductPoints(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet not found',
            ], 404);
        }

        $success = $wallet->deductPoints($request->points);

        if (!$success) {
            return response()->json([
                'message' => 'Insufficient points',
                'available' => $wallet->total_points,
            ], 400);
        }

        return response()->json([
            'message' => 'Points deducted successfully',
            'wallet' => $wallet->fresh(),
            'total_points' => $wallet->total_points,
        ]);
    }

    /**
     * Get transaction history (placeholder for future)
     */
    public function transactions(Request $request)
    {
        // Placeholder for transaction history
        return response()->json([
            'transactions' => [],
            'message' => 'Transaction history coming soon',
        ]);
    }
}

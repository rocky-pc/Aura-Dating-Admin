<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Web index for admin panel
     */
    public function webIndex(Request $request)
    {
        $query = Wallet::with('user.profile')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('min_balance')) {
            $query->where('balance', '>=', $request->min_balance);
        }

        if ($request->has('has_points')) {
            $query->whereRaw('(balance + bonus_points) > 0');
        }

        $wallets = $query->paginate($request->get('per_page', 20));

        return view('admin.wallets.index', compact('wallets'));
    }

    /**
     * List all wallets with user info
     */
    public function index(Request $request)
    {
        $query = Wallet::with('user.profile')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('min_balance')) {
            $query->where('balance', '>=', $request->min_balance);
        }

        if ($request->has('has_points')) {
            $query->whereRaw('(balance + bonus_points) > 0');
        }

        $wallets = $query->paginate($request->get('per_page', 20));

        return response()->json($wallets);
    }

    /**
     * Get single wallet details
     */
    public function show(Wallet $wallet)
    {
        $wallet->load('user.profile');

        return response()->json([
            'wallet' => $wallet,
            'total_points' => $wallet->total_points,
        ]);
    }

    /**
     * Web add points for admin panel
     */
    public function webAddPoints(Request $request, Wallet $wallet)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'type' => 'sometimes|in:balance,bonus',
        ]);

        $type = $request->get('type', 'bonus');
        
        if ($type === 'bonus') {
            $wallet->increment('bonus_points', $request->points);
        } else {
            $wallet->increment('balance', $request->points);
        }

        return back()->with('success', 'Points added successfully');
    }

    /**
     * Add points to a user's wallet
     */
    public function addPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'type' => 'sometimes|in:balance,bonus',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
        
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
        ]);
    }

    /**
     * Web deduct points for admin panel
     */
    public function webDeductPoints(Request $request, Wallet $wallet)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $success = $wallet->deductPoints($request->points);

        if (!$success) {
            return back()->with('error', 'Insufficient points');
        }

        return back()->with('success', 'Points deducted successfully');
    }

    /**
     * Deduct points from a user's wallet
     */
    public function deductPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
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
        ]);
    }

    /**
     * Web reset wallet for admin panel
     */
    public function webReset(Request $request, Wallet $wallet)
    {
        $wallet->update([
            'balance' => 0,
            'bonus_points' => 0,
        ]);

        return back()->with('success', 'Wallet reset successfully');
    }

    /**
     * Reset wallet (set all to zero)
     */
    public function reset(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $wallet = Wallet::where('user_id', $request->user_id)->first();

        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet not found',
            ], 404);
        }

        $wallet->update([
            'balance' => 0,
            'bonus_points' => 0,
        ]);

        return response()->json([
            'message' => 'Wallet reset successfully',
            'wallet' => $wallet,
        ]);
    }

    /**
     * Get wallet settings
     */
    public function settings()
    {
        $settings = WalletSettings::all();

        return response()->json([
            'settings' => $settings,
        ]);
    }

    /**
     * Update wallet settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        WalletSettings::updateOrCreate(
            ['key' => $request->key],
            [
                'value' => $request->value,
                'description' => $request->description,
            ]
        );

        return response()->json([
            'message' => 'Settings updated successfully',
        ]);
    }

    /**
     * Get wallet statistics
     */
    public function stats()
    {
        $stats = [
            'total_wallets' => Wallet::count(),
            'total_points_in_system' => Wallet::sum(DB::raw('balance + bonus_points')),
            'total_balance' => Wallet::sum('balance'),
            'total_bonus_points' => Wallet::sum('bonus_points'),
            'total_spent' => Wallet::sum('total_spent'),
            'wallets_with_points' => Wallet::whereRaw('(balance + bonus_points) > 0')->count(),
        ];

        return response()->json($stats);
    }
}

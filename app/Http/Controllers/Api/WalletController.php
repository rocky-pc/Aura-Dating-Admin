<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletSettings;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Get current user's wallet
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

        return response()->json([
            'wallet' => $wallet,
            'total_points' => $wallet->total_points,
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

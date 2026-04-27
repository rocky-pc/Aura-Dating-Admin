<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    /**
     * Web index for admin panel
     */
    public function webIndex(Request $request)
    {
        $query = Wallet::with('user.profile')->whereHas('user')->orderBy('created_at', 'desc');

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

        // Calculate statistics for the view
        $totalBalance = Wallet::sum('balance');
        $totalBonus = Wallet::sum('bonus_points');
        $zeroWallets = Wallet::whereRaw('(balance + bonus_points) = 0')->count();

        // Today's activity (placeholder data - in a real app, you'd track these)
        $todayAdded = 24; // Points added today
        $todayResets = 3; // Resets done today
        $todayVolume = 4800; // Points distributed today
        $newWallets = 12; // New wallets created today

        return view('admin.wallets.index', compact(
            'wallets',
            'totalBalance',
            'totalBonus',
            'zeroWallets',
            'todayAdded',
            'todayResets',
            'todayVolume',
            'newWallets'
        ));
    }

    /**
     * List all wallets with user info
     */
    public function index(Request $request)
    {
        $query = Wallet::with('user.profile')->whereHas('user')->orderBy('created_at', 'desc');

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
        $points = $request->points;

        try {
            if ($type === 'bonus') {
                $wallet->increment('bonus_points', $points);
            } else {
                $wallet->increment('balance', $points);
            }

            // Log the action
            $userEmail = $wallet->user->email ?? 'unknown';
            Log::info("Admin added {$points} {$type} points to wallet {$wallet->id} for user {$userEmail}");

            $userName = $wallet->user->profile->first_name ?? $wallet->user->email ?? 'user';
            $message = ucfirst($type) . " points ({$points}) added successfully to {$userName}";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'wallet' => $wallet->fresh(),
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Failed to add points to wallet {$wallet->id}: " . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add points. Please try again.',
                ], 500);
            }

            return back()->with('error', 'Failed to add points. Please try again.');
        }
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
        try {
            $wallet->update([
                'balance' => 0,
                'bonus_points' => 0,
            ]);

            $userName = $wallet->user->profile->first_name ?? $wallet->user->email ?? 'user';
            Log::info("Admin reset wallet {$wallet->id} for user {$userName}");

            $message = "Wallet reset successfully for {$userName}";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'wallet' => $wallet->fresh(),
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Failed to reset wallet {$wallet->id}: " . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reset wallet. Please try again.',
                ], 500);
            }

            return back()->with('error', 'Failed to reset wallet. Please try again.');
        }
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
     * Export wallets as CSV
     */
    public function export(Request $request)
    {
        $query = Wallet::with('user.profile')->whereHas('user')->orderBy('created_at', 'desc');

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

        $wallets = $query->get();

        $filename = 'wallets_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($wallets) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'User ID',
                'Email',
                'First Name',
                'Last Name',
                'Balance',
                'Bonus Points',
                'Total Points',
                'Total Spent',
                'Lifetime Earnings',
                'Created At',
                'Updated At'
            ]);

            // CSV data
            foreach ($wallets as $wallet) {
                fputcsv($file, [
                    $wallet->user->id,
                    $wallet->user->email,
                    $wallet->user->profile->first_name ?? '',
                    $wallet->user->profile->last_name ?? '',
                    $wallet->balance,
                    $wallet->bonus_points,
                    $wallet->balance + $wallet->bonus_points,
                    $wallet->total_spent,
                    $wallet->lifetime_earnings,
                    $wallet->created_at->format('Y-m-d H:i:s'),
                    $wallet->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

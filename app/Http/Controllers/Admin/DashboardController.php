<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\UserSwipe;
use App\Models\Subscription;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard analytics (API)
     */
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $verifiedUsers = User::where('is_verified', true)->count();
        $premiumUsers = User::where('is_premium', true)->count();
        
        $totalMatches = UserMatch::where('is_active', true)->count();
        $newMatchesToday = UserMatch::whereDate('matched_at', today())->count();
        
        $activeSubscriptions = Subscription::where('is_active', true)->count();
        
        $pendingReports = UserReport::where('status', 'pending')->count();
        $resolvedReports = UserReport::where('status', '!=', 'pending')->count();
        
        // Users by registration date (last 7 days)
        $usersByDate = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Matches by date (last 7 days)
        $matchesByDate = UserMatch::select(DB::raw('DATE(matched_at) as date'), DB::raw('count(*) as count'))
            ->whereDate('matched_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return response()->json([
            'users' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'verified' => $verifiedUsers,
                'premium' => $premiumUsers,
            ],
            'matches' => [
                'total' => $totalMatches,
                'today' => $newMatchesToday,
            ],
            'subscriptions' => [
                'active' => $activeSubscriptions,
            ],
            'reports' => [
                'pending' => $pendingReports,
                'resolved' => $resolvedReports,
            ],
            'charts' => [
                'users_by_date' => $usersByDate,
                'matches_by_date' => $matchesByDate,
            ],
        ]);
    }

    /**
     * Web dashboard view
     */
    public function webIndex()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'verified_users' => User::where('is_verified', true)->count(),
            'premium_users' => User::where('is_premium', true)->count(),
            'total_matches' => UserMatch::where('is_active', true)->count(),
            'total_swipes' => UserSwipe::count(),
            'active_subscriptions' => Subscription::where('is_active', true)->count(),
            'pending_reports' => UserReport::where('status', 'pending')->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    /**
     * Get system health stats
     */
    public function health()
    {
        return response()->json([
            'database' => [
                'connections' => DB::connection()->getDatabaseName(),
            ],
            'app' => [
                'version' => config('app.version', '1.0.0'),
                'environment' => config('app.env'),
            ],
        ]);
    }
}

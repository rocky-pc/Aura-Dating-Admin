<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSwipe;
use App\Models\UserMatch;
use App\Models\ProfileImage;
use App\Models\UserReport;
use App\Models\Message;
use App\Models\Like;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Web - Analytics dashboard
     */
    public function webIndex(Request $request)
    {
        // User metrics
        $userStats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'verified' => User::where('is_verified', true)->count(),
            'premium' => User::where('is_premium', true)->count(),
            'new_30' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Engagement metrics
        $engagementStats = [
            'messages' => Message::count(),
            'messages_30' => Message::where('created_at', '>=', now()->subDays(30))->count(),
            'likes' => Like::count(),
            'matches' => UserMatch::count(),
            'matches_30' => UserMatch::where('created_at', '>=', now()->subDays(30))->count(),
            'swipes' => UserSwipe::count(),
        ];

        // Content metrics
        $contentStats = [
            'profiles' => User::whereHas('profile')->count(),
            'images' => ProfileImage::count(),
            'reports' => UserReport::count(),
        ];

        $stats = [
            'users' => $userStats,
            'engagement' => $engagementStats,
            'content' => $contentStats,
        ];

        return view('admin.analytics.index', compact('stats'));
    }
}
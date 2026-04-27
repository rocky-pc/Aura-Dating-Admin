<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PremiumController extends Controller
{
    /**
     * Web - List all premium users with pagination and filters
     */
    public function webIndex(Request $request)
    {
        $query = User::where('is_premium', true)->with('profile')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->paginate(20);

        $stats = [
            'total' => User::where('is_premium', true)->count(),
            'active' => User::where('is_premium', true)->where('is_active', true)->count(),
            'expiring_soon' => User::where('is_premium', true)
                ->where('premium_expires_at', '<=', now()->addDays(7))
                ->where('premium_expires_at', '>', now())
                ->count(),
            'verified' => User::where('is_premium', true)->where('is_verified', true)->count(),
        ];

        return view('admin.premium.index', compact('users', 'stats'));
    }
}
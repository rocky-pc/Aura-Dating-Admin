<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSwipe;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function webIndex(Request $request)
    {
        $query = UserSwipe::with(['swiper', 'swiped'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->has('action') && $request->action !== '') {
            $query->where('action', $request->action);
        }

        // Search users
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('swiper', function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%");
            })->orWhereHas('swiped', function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%");
            });
        }

        $swipes = $query->paginate(20);

        // Statistics
        $stats = [
            'likes' => UserSwipe::where('action', 'like')->count(),
            'dislikes' => UserSwipe::where('action', 'dislike')->count(),
            'super_likes' => UserSwipe::where('action', 'super_like')->count(),
            'favorites' => UserSwipe::where('action', 'favorite')->count(),
            'matches' => UserSwipe::where('is_match', true)->count(),
        ];

        return view('admin.favorites.index', compact('swipes', 'stats'));
    }

    public function destroy(UserSwipe $swipe)
    {
        $swipe->delete();
        
        return redirect()->back()->with('success', 'Interaction deleted successfully');
    }
}

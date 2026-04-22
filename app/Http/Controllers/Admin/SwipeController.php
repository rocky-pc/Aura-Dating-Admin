<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSwipe;
use Illuminate\Http\Request;

class SwipeController extends Controller
{
    public function index(Request $request)
    {
        $query = UserSwipe::with(['swiper.profile', 'swiper.images', 'swiped.profile', 'swiped.images'])
            ->orderBy('created_at', 'desc');

        if ($request->has('action') && $request->action !== '') {
            $action = $request->action;
            if ($action === 'match') {
                $query->where('is_match', true);
            } else {
                $query->where('action', $action);
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('swiper', function ($sq) use ($search) {
                    $sq->where('email', 'like', "%{$search}%")
                       ->orWhereHas('profile', function ($pq) use ($search) {
                           $pq->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                       });
                })
                ->orWhereHas('swiped', function ($sq) use ($search) {
                    $sq->where('email', 'like', "%{$search}%")
                       ->orWhereHas('profile', function ($pq) use ($search) {
                           $pq->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                       });
                });
            });
        }

        $swipes = $query->paginate(20);

        $stats = [
            'total' => UserSwipe::count(),
            'likes' => UserSwipe::where('action', 'like')->count(),
            'dislikes' => UserSwipe::where('action', 'dislike')->count(),
            'super_likes' => UserSwipe::where('action', 'super_like')->count(),
            'matches' => UserSwipe::where('is_match', true)->count(),
        ];

        return view('admin.swipes.index', compact('swipes', 'stats'));
    }

    public function show($id)
    {
        $swipe = UserSwipe::with(['swiper.profile', 'swiper.images', 'swiped.profile', 'swiped.images'])
            ->findOrFail($id);

        return view('admin.swipes.show', compact('swipe'));
    }

    public function destroy($id)
    {
        $swipe = UserSwipe::findOrFail($id);
        $swipe->delete();

        return redirect()->route('admin.swipes.index')
            ->with('success', 'Swipe deleted successfully');
    }
}
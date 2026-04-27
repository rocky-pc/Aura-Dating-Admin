<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Display matches management page
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Like::with(['sender.profile', 'sender.images', 'receiver.profile', 'receiver.images'])
            ->whereHas('sender')
            ->whereHas('receiver');

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('sender', function ($sq) use ($search) {
                    $sq->where('email', 'like', "%{$search}%")
                       ->orWhereHas('profile', function ($pq) use ($search) {
                           $pq->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                       });
                })
                ->orWhereHas('receiver', function ($sq) use ($search) {
                    $sq->where('email', 'like', "%{$search}%")
                       ->orWhereHas('profile', function ($pq) use ($search) {
                           $pq->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                       });
                });
            });
        }

        $likes = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get stats
        $stats = [
            'total' => Like::count(),
            'pending' => Like::where('status', 'pending')->count(),
            'accepted' => Like::where('status', 'accepted')->count(),
            'rejected' => Like::where('status', 'rejected')->count(),
        ];

        return view('admin.matches.index', compact('likes', 'stats', 'status', 'search'));
    }

    /**
     * Show match details
     */
    public function show($id)
    {
        $like = Like::with(['sender.profile', 'sender.images', 'receiver.profile', 'receiver.images'])
            ->whereHas('sender')
            ->whereHas('receiver')
            ->findOrFail($id);

        return view('admin.matches.show', compact('like'));
    }

    /**
     * Delete a like/match
     */
    public function destroy($id)
    {
        $like = Like::findOrFail($id);
        $like->delete();

        return redirect()->route('admin.matches.index')
            ->with('success', 'Like/Match deleted successfully');
    }
}
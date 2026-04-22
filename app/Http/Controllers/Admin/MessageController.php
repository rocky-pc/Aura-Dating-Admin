<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserMatch;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = UserMatch::with(['userOne.profile', 'userOne.images', 'userTwo.profile', 'userTwo.images', 'conversation'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('userOne', function ($sq) use ($search) {
                    $sq->where('email', 'like', "%{$search}%")
                       ->orWhereHas('profile', function ($pq) use ($search) {
                           $pq->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                       });
                })
                ->orWhereHas('userTwo', function ($sq) use ($search) {
                    $sq->where('email', 'like', "%{$search}%")
                       ->orWhereHas('profile', function ($pq) use ($search) {
                           $pq->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                       });
                });
            });
        }

        $conversations = $query->paginate(20);

        $stats = [
            'total_matches' => UserMatch::where('is_active', true)->count(),
            'total_messages' => Message::count(),
            'unread_messages' => Message::where('is_read', false)->count(),
        ];

        return view('admin.messages.index', compact('conversations', 'stats'));
    }

    public function show($id)
    {
        $match = UserMatch::with([
            'userOne.profile',
            'userOne.images',
            'userTwo.profile', 
            'userTwo.images',
            'conversation.messages.sender'
        ])->findOrFail($id);

        $messages = $match->conversation?->messages()->orderBy('created_at', 'asc')->get() ?? collect();

        return view('admin.messages.show', compact('match', 'messages'));
    }

    public function destroy($id)
    {
        $match = UserMatch::findOrFail($id);
        $match->update(['is_active' => false]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Conversation unmatched successfully');
    }
}
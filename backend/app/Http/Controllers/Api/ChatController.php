<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMatch;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\ConnectionRequest;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get all conversations for the user
     */
    public function conversations(Request $request)
    {
        $user = $request->user();
        
        // Get matches with their conversations
        $matches = UserMatch::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->with(['conversation', 'userOne', 'userOne.profile', 'userOne.images' => function ($q) {
                $q->where('is_primary', true);
            }, 'userTwo', 'userTwo.profile', 'userTwo.images' => function ($q) {
                $q->where('is_primary', true);
            }])
            ->get()
            ->map(function ($match) use ($user) {
                $otherUser = $match->user_one_id === $user->id ? $match->userTwo : $match->userOne;
                $conversation = $match->conversation;
                
                // Get last message
                $lastMessage = $conversation ? $conversation->messages()->latest()->first() : null;
                
                // Get unread count
                $unreadCount = $conversation 
                    ? $conversation->messages()
                        ->where('sender_id', '!=', $user->id)
                        ->where('is_read', false)
                        ->count()
                    : 0;
                
                return [
                    'match_id' => $match->id,
                    'conversation_id' => $conversation?->id,
                    'other_user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'matched_at' => $match->matched_at,
                ];
            })
            ->sortByDesc(function ($conv) {
                return $conv['last_message']?->created_at ?? $conv['matched_at'];
            })
            ->values();
        
        return response()->json([
            'conversations' => $matches,
        ]);
    }

    /**
     * Get messages in a conversation
     */
    public function messages(Request $request, $conversationId)
    {
        $user = $request->user();
        
        // Find conversation through user's matches
        $match = UserMatch::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->whereHas('conversation', function ($q) use ($conversationId) {
                $q->where('id', $conversationId);
            })
            ->firstOrFail();
        
        $conversation = $match->conversation;
        
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate($request->get('per_page', 50));
        
        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        return response()->json([
            'messages' => $messages,
            'conversation' => $conversation,
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        
        $user = $request->user();
        
        // Find conversation through user's matches
        $match = UserMatch::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->whereHas('conversation', function ($q) use ($conversationId) {
                $q->where('id', $conversationId);
            })
            ->firstOrFail();
        
        $conversation = $match->conversation;
        
        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);
        
        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);
        
        // Determine recipient
        $recipientId = $match->user_one_id === $user->id ? $match->user_two_id : $match->user_one_id;
        
        // Send notification
        Notification::create([
            'user_id' => $recipientId,
            'type' => 'message',
            'title' => 'New Message',
            'body' => $user->profile->first_name . ': ' . substr($request->message, 0, 50),
            'data' => [
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
            ],
        ]);
        
        return response()->json([
            'message' => $message->load('sender'),
        ], 201);
    }

    /**
     * Create new conversation for accepted connection
     */
    public function createConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = $request->user();
        $otherUserId = $request->user_id;

        // Check if connection exists
        $connection = ConnectionRequest::where(function ($q) use ($user, $otherUserId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $otherUserId);
        })->orWhere(function ($q) use ($user, $otherUserId) {
            $q->where('sender_id', $otherUserId)->where('receiver_id', $user->id);
        })->where('status', 'accepted')->first();

        if (!$connection) {
            return response()->json([
                'message' => 'No accepted connection found with this user',
            ], 403);
        }

        // Check for existing conversation
        $existingMatch = UserMatch::where(function ($q) use ($user, $otherUserId) {
            $q->where('user_one_id', $user->id)->where('user_two_id', $otherUserId);
        })->orWhere(function ($q) use ($user, $otherUserId) {
            $q->where('user_one_id', $otherUserId)->where('user_two_id', $user->id);
        })->first();

        if ($existingMatch) {
            return response()->json([
                'success' => true,
                'conversation' => [
                    'conversation_id' => $existingMatch->conversation?->id,
                ],
            ]);
        }

        // Create match and conversation
        $match = UserMatch::create([
            'user_one_id' => min($user->id, $otherUserId),
            'user_two_id' => max($user->id, $otherUserId),
            'is_active' => true,
            'matched_at' => now(),
        ]);

        $conversation = $match->conversation()->create([
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'conversation' => [
                'conversation_id' => $conversation->id,
            ],
        ]);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request, $messageId)
    {
        $message = Message::whereHas('conversation', function ($q) use ($request) {
            $q->whereHas('match', function ($q2) use ($request) {
                $q2->where('user_one_id', $request->user()->id)
                    ->orWhere('user_two_id', $request->user()->id);
            });
        })->findOrFail($messageId);
        
        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json([
            'message' => 'Message marked as read',
        ]);
    }

    /**
     * Get unread message count
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        
        $count = Message::whereHas('conversation.match', function ($q) use ($user) {
            $q->where('user_one_id', $user->id)
                ->orWhere('user_two_id', $user->id);
        })
        ->where('sender_id', '!=', $user->id)
        ->where('is_read', false)
        ->count();
        
        return response()->json([
            'unread_count' => $count,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\ConnectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Get all conversations for the user
     */
    public function conversations(Request $request)
    {
        try {
            $user = $request->user();

            // Get conversations with basic user info, ordered by last message time
            $conversations = UserMatch::where('is_active', true)
                ->where(function ($q) use ($user) {
                    $q->where('user_one_id', $user->id)
                        ->orWhere('user_two_id', $user->id);
                })
                ->whereHas('conversation.messages') // Only include conversations with messages
                ->with(['conversation' => function ($query) {
                    $query->with(['messages' => function ($q) {
                        $q->latest()->limit(1); // Get only the latest message
                    }]);
                }])
                ->get()
                ->sortByDesc(function ($match) {
                    // Sort by last message time, fallback to match creation time
                    return $match->conversation?->messages?->first()?->created_at ?? $match->created_at;
                })
                ->take(20)
                ->map(function ($match) use ($user) {
                    $otherUserId = $match->user_one_id === $user->id ? $match->user_two_id : $match->user_one_id;

                    // Get basic user info
                    $otherUser = User::find($otherUserId);

                    // Skip admin/moderator users
                    if (!$otherUser || in_array($otherUser->role, ['admin', 'moderator'])) {
                        return null;
                    }

                    $firstName = 'User';
                    $profileImage = null;

                    if ($otherUser->profile) {
                        $firstName = $otherUser->profile->first_name ?: 'User';

                        // Get primary profile image
                        if ($otherUser->images && $otherUser->images->count() > 0) {
                            $primaryImage = $otherUser->images->where('is_primary', true)->first();
                            if (!$primaryImage) {
                                $primaryImage = $otherUser->images->first(); // fallback to first image
                            }
                            if ($primaryImage) {
                                $profileImage = $primaryImage->image_url;
                            }
                        }
                    }

                    // Ensure conversation exists
                    $conversation = $match->conversation;
                    if (!$conversation) {
                        $conversation = $match->conversation()->create([]);
                    }

                    // Get last message
                    $lastMessage = $conversation->messages()->latest()->first();

                    // Get unread count
                    $unreadCount = $conversation->messages()
                        ->where('sender_id', '!=', $user->id)
                        ->where('is_read', false)
                        ->count();

                    return [
                        'match_id' => $match->id,
                        'conversation_id' => $conversation->id,
                        'other_user' => [
                            'id' => $otherUserId,
                            'first_name' => $firstName,
                            'profile' => [
                                'first_name' => $firstName,
                                'profile_image' => $profileImage,
                            ],
                        ],
                        'last_message' => $lastMessage ? [
                            'message' => $lastMessage->message_content,
                            'created_at' => $lastMessage->created_at->toISOString(),
                        ] : null,
                        'unread_count' => $unreadCount,
                        'matched_at' => $match->created_at?->toISOString(),
                    ];
                })->filter()->values();

            return response()->json([
                'conversations' => $conversations,
            ]);
        } catch (\Exception $e) {
            Log::error('conversations error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Server error'
            ], 500);
        }
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
            ->orderBy('created_at', 'asc') // Oldest messages first, newest last
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

        // Determine recipient
        $recipientId = $match->user_one_id === $user->id ? $match->user_two_id : $match->user_one_id;

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $recipientId,
            'message_content' => $request->message,
            'is_read' => false,
        ]);

        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);

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
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_one_id' => min($user->id, $otherUserId),
            'user_two_id' => max($user->id, $otherUserId),
            'is_active' => true,
        ]);

        $conversation = $match->conversation()->create([]);

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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMatch;
use App\Models\Conversation;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    /**
     * Get all matches for the user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $matches = UserMatch::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->with(['userOne', 'userOne.profile', 'userOne.images' => function ($q) {
                $q->where('is_primary', true);
            }, 'userTwo', 'userTwo.profile', 'userTwo.images' => function ($q) {
                $q->where('is_primary', true);
            }, 'conversation'])
            ->orderBy('matched_at', 'desc')
            ->paginate($request->get('per_page', 20));
        
        // Add other user info to each match
        $matches->getCollection()->transform(function ($match) use ($user) {
            $otherUser = $match->user_one_id === $user->id ? $match->userTwo : $match->userOne;
            unset($match->userOne, $match->userTwo);
            $match->other_user = $otherUser;
            return $match;
        });
        
        return response()->json($matches);
    }

    /**
     * Get single match details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $match = UserMatch::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->where('id', $id)
            ->with(['userOne.profile', 'userOne.images', 'userTwo.profile', 'userTwo.images', 'conversation'])
            ->firstOrFail();
        
        $otherUser = $match->user_one_id === $user->id ? $match->userOne : $match->userTwo;
        
        return response()->json([
            'match' => $match,
            'other_user' => $otherUser,
        ]);
    }

    /**
     * Unmatch with a user
     */
    public function unmatch(Request $request, $id)
    {
        $user = $request->user();
        
        $match = UserMatch::where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)
                ->orWhere('user_two_id', $user->id);
        })
        ->where('id', $id)
        ->firstOrFail();
        
        // Deactivate match
        $match->update([
            'is_active' => false,
            'unmatched_at' => now(),
        ]);
        
        // Deactivate conversation
        if ($match->conversation) {
            $match->conversation->update(['is_active' => false]);
        }
        
        return response()->json([
            'message' => 'Successfully unmatched',
        ]);
    }

    /**
     * Send a like to another user
     */
    public function like(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|string|exists:users,uuid',
        ]);

        $user = $request->user();
        $targetUser = User::where('uuid', $request->target_user_id)->first();

        if (!$targetUser) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }

        if ($user->id === $targetUser->id) {
            return response()->json(['success' => false, 'error' => 'Cannot like yourself'], 400);
        }

        DB::beginTransaction();
        try {
            // Check for existing like relationship
            $existingLike = Like::where(function ($q) use ($user, $targetUser) {
                $q->where('sender_id', $user->id)->where('receiver_id', $targetUser->id)
                  ->orWhere('sender_id', $targetUser->id)->where('receiver_id', $user->id);
            })->first();

            if ($existingLike) {
                if ($existingLike->status === 'accepted') {
                    DB::rollBack();
                    return response()->json([
                        'success' => true,
                        'message' => 'Already matched with this user',
                        'is_match' => true
                    ]);
                }

                if ($existingLike->status === 'pending') {
                    // If the other user already liked us, create match
                    if ($existingLike->sender_id === $targetUser->id) {
                        $existingLike->update(['status' => 'accepted']);

                        // Create conversation
                        $this->createConversation($user->id, $targetUser->id);

                        DB::commit();
                        return response()->json([
                            'success' => true,
                            'message' => 'Match created!',
                            'is_match' => true
                        ]);
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'success' => true,
                            'message' => 'Like already sent'
                        ]);
                    }
                }

                if ($existingLike->status === 'rejected') {
                    // Allow re-liking after rejection
                    $existingLike->update(['status' => 'pending']);
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Like sent successfully',
                        'like_id' => $existingLike->id
                    ]);
                }
            }

            // Create new like
            $like = Like::create([
                'sender_id' => $user->id,
                'receiver_id' => $targetUser->id,
                'status' => 'pending'
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Like sent successfully',
                'like_id' => $like->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to send like'
            ], 500);
        }
    }

    /**
     * Accept or reject a like request
     */
    public function likeAction(Request $request)
    {
        $request->validate([
            'like_id' => 'required|integer|exists:likes,id',
            'action' => 'required|in:accept,reject',
        ]);

        $user = $request->user();

        $like = Like::where('id', $request->like_id)
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$like) {
            return response()->json([
                'success' => false,
                'error' => 'Like not found or not authorized'
            ], 404);
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'accept') {
                $like->update(['status' => 'accepted']);

                // Create conversation
                $this->createConversation($user->id, $like->sender_id);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Like accepted! You can now chat.',
                    'is_match' => true
                ]);
            } else {
                $like->update(['status' => 'rejected']);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Like rejected'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to process action'
            ], 500);
        }
    }

    /**
     * Get user's matches and pending likes
     */
    public function getMatches(Request $request)
    {
        $user = $request->user();

        // Get sent pending likes (you liked them, waiting)
        $sentPending = Like::where('sender_id', $user->id)
            ->where('status', 'pending')
            ->with(['receiver.profile', 'receiver.images'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($like) {
                return array_merge($like->receiver->toArray(), [
                    'like_id' => $like->id,
                    'liked_at' => $like->created_at,
                ]);
            });

        // Get received pending likes (they liked you, waiting for accept)
        $receivedPending = Like::where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->with(['sender.profile', 'sender.images'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($like) {
                return array_merge($like->sender->toArray(), [
                    'like_id' => $like->id,
                    'liked_at' => $like->created_at,
                ]);
            });

        // Get accepted matches
        $matches = Like::where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
            })
            ->where('status', 'accepted')
            ->with(['sender.profile', 'sender.images', 'receiver.profile', 'receiver.images'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($like) use ($user) {
                $otherUser = $like->sender_id === $user->id ? $like->receiver : $like->sender;
                return array_merge($otherUser->toArray(), [
                    'like_id' => $like->id,
                    'matched_at' => $like->updated_at,
                    'match_type' => $like->sender_id === $user->id ? 'sent' : 'received',
                ]);
            });

        return response()->json([
            'success' => true,
            'data' => [
                'sent_pending' => $sentPending,
                'received_pending' => $receivedPending,
                'matches' => $matches,
            ]
        ]);
    }

    /**
     * Create conversation between two users
     */
    private function createConversation($user1Id, $user2Id)
    {
        // Ensure consistent ordering (smaller ID first)
        $u1 = min($user1Id, $user2Id);
        $u2 = max($user1Id, $user2Id);

        // Check if conversation exists
        $existing = Conversation::where(function ($q) use ($u1, $u2) {
            $q->where('user_one_id', $u1)->where('user_two_id', $u2)
              ->orWhere('user_one_id', $u2)->where('user_two_id', $u1);
        })->first();

        if (!$existing) {
            Conversation::create([
                'user_one_id' => $u1,
                'user_two_id' => $u2,
                'is_active' => true,
            ]);
        }
    }
}

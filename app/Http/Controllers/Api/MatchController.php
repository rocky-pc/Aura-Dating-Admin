<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMatch;
use App\Models\Conversation;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

            // Filter out admin/moderator users from matches
            if ($otherUser && in_array($otherUser->role, ['admin', 'moderator'])) {
                return null; // This will be filtered out
            }

            unset($match->userOne, $match->userTwo);
            $match->other_user = $otherUser;
            return $match;
        })->filter(); // Remove null entries
        
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

        // Prevent showing admin/moderator user details
        if (in_array($otherUser->role, ['admin', 'moderator'])) {
            return response()->json(['error' => 'Match not found'], 404);
        }

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

        if ($targetUser->role !== 'user') {
            Log::warning('User attempted to like non-user account', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'target_user_id' => $targetUser->id,
                'target_user_role' => $targetUser->role,
                'target_user_uuid' => $targetUser->uuid,
            ]);
            return response()->json(['success' => false, 'error' => 'Cannot interact with this user'], 403);
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

                        // Check if match already exists
                        $existingMatch = UserMatch::where(function ($q) use ($user, $targetUser) {
                            $q->where('user_one_id', min($user->id, $targetUser->id))
                              ->where('user_two_id', max($user->id, $targetUser->id));
                        })->first();

                        if (!$existingMatch) {
                            // Create match record
                            $match = UserMatch::create([
                                'uuid' => (string) Str::uuid(),
                                'user_one_id' => min($user->id, $targetUser->id),
                                'user_two_id' => max($user->id, $targetUser->id),
                                'is_active' => true,
                            ]);

                            // Create conversation for the match
                            Conversation::create([
                                'match_id' => $match->id,
                            ]);
                        }

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
            'like_id' => 'required|numeric|exists:likes,id',
            'action' => 'required|in:accept,reject',
        ]);

        $user = $request->user();

        // Debug logging
        Log::info('likeAction called', [
            'like_id' => $request->like_id,
            'like_id_type' => gettype($request->like_id),
            'action' => $request->action,
            'user_id' => $user->id
        ]);

        $like = Like::where('id', $request->like_id)
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        Log::info('Like query result', [
            'like_found' => $like ? 'yes' : 'no',
            'like_id' => $request->like_id,
            'receiver_id' => $user->id
        ]);

        if (!$like) {
            return response()->json([
                'success' => false,
                'error' => 'Like not found or not authorized'
            ], 404);
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'accept') {
                Log::info('Accepting like', [
                    'like_id' => $like->id,
                    'sender_id' => $like->sender_id,
                    'receiver_id' => $user->id
                ]);

                $like->update(['status' => 'accepted']);

                // Check if match already exists
                $existingMatch = UserMatch::where(function ($q) use ($user, $like) {
                    $q->where('user_one_id', min($user->id, $like->sender_id))
                      ->where('user_two_id', max($user->id, $like->sender_id));
                })->first();

                Log::info('Existing match check', [
                    'existing_match' => $existingMatch ? 'found' : 'not found',
                    'user_one_id' => min($user->id, $like->sender_id),
                    'user_two_id' => max($user->id, $like->sender_id)
                ]);

                if (!$existingMatch) {
                    try {
                        // Create match record
                        $match = UserMatch::create([
                            'uuid' => (string) Str::uuid(),
                            'user_one_id' => min($user->id, $like->sender_id),
                            'user_two_id' => max($user->id, $like->sender_id),
                            'is_active' => true,
                        ]);

                        Log::info('UserMatch created successfully', [
                            'match_id' => $match->id,
                            'user_one_id' => $match->user_one_id,
                            'user_two_id' => $match->user_two_id
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to create UserMatch', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id,
                            'sender_id' => $like->sender_id
                        ]);
                        // Continue without match record for now
                        $match = null;
                    }

                    Log::info('Created new match', [
                        'match_id' => $match->id,
                        'user_one_id' => $match->user_one_id,
                        'user_two_id' => $match->user_two_id
                    ]);

                    // Create conversation for the match
                    $conversation = Conversation::create([
                        'match_id' => $match->id,
                    ]);

                    Log::info('Created conversation', [
                        'conversation_id' => $conversation->id,
                        'match_id' => $match->id
                    ]);
                } else {
                    $match = $existingMatch;
                    Log::info('Using existing match', ['match_id' => $match->id]);
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Like accepted! You can now chat.',
                    'is_match' => true,
                    'match' => $match
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
                'error' => 'Failed to process action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's matches and pending likes
     */
    public function getMatches(Request $request)
    {
        try {
            $user = $request->user();

            Log::info('getMatches called', ['user_id' => $user->id]);

            // Get sent pending likes (you liked them, waiting)
            $sentPending = Like::where('sender_id', $user->id)
                ->where('status', 'pending')
                ->with(['receiver:id,email'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($like) {
                    $receiver = $like->receiver;

                    // Skip admin/moderator users
                    if (!$receiver || in_array($receiver->role, ['admin', 'moderator'])) {
                        return null;
                    }

                    $firstName = 'User';
                    $profileImage = null;

                    if ($receiver->profile) {
                        $firstName = $receiver->profile->first_name ?: 'User';

                        // Get primary profile image
                        if ($receiver->images && $receiver->images->count() > 0) {
                            $primaryImage = $receiver->images->where('is_primary', true)->first();
                            if (!$primaryImage) {
                                $primaryImage = $receiver->images->first();
                            }
                            if ($primaryImage) {
                                $profileImage = $primaryImage->image_url;
                            }
                        }
                    }

                    return [
                        'id' => $receiver->id,
                        'like_id' => $like->id,
                        'first_name' => $firstName,
                        'profile_image' => $profileImage,
                        'liked_at' => $like->created_at->toISOString(),
                    ];
                })->filter()->values();

            // Get received pending likes (they liked you, waiting for accept)
            $receivedPending = Like::where('receiver_id', $user->id)
                ->where('status', 'pending')
                ->with(['sender:id,email'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($like) {
                    $sender = $like->sender;

                    // Skip admin/moderator users
                    if (!$sender || in_array($sender->role, ['admin', 'moderator'])) {
                        return null;
                    }

                    $firstName = 'User';
                    $profileImage = null;

                    if ($sender->profile) {
                        $firstName = $sender->profile->first_name ?: 'User';

                        // Get primary profile image
                        if ($sender->images && $sender->images->count() > 0) {
                            $primaryImage = $sender->images->where('is_primary', true)->first();
                            if (!$primaryImage) {
                                $primaryImage = $sender->images->first();
                            }
                            if ($primaryImage) {
                                $profileImage = $primaryImage->image_url;
                            }
                        }
                    }

                    return [
                        'id' => $sender->id,
                        'like_id' => $like->id,
                        'first_name' => $firstName,
                        'profile_image' => $profileImage,
                        'liked_at' => $like->created_at->toISOString(),
                    ];
                })->filter()->values();

            // Get accepted matches with basic user info
            $matches = UserMatch::where(function ($q) use ($user) {
                    $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
                })
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get()
                ->map(function ($match) use ($user) {
                    $otherUserId = $match->user_one_id === $user->id ? $match->user_two_id : $match->user_one_id;

                    // Get basic user info without complex relationships
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

                    return [
                        'id' => $match->id,
                        'user' => [
                            'id' => $otherUserId,
                            'first_name' => $firstName,
                            'profile_image' => $profileImage,
                        ],
                        'matched_at' => $match->created_at?->toISOString(),
                    ];
                })->filter()->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'sent_pending' => $sentPending,
                    'received_pending' => $receivedPending,
                    'matches' => $matches,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('getMatches error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Server error'
            ], 500);
        }
    }

    /**
     * Create conversation for a match
     */
    private function createConversation($user1Id, $user2Id)
    {
        // Find the match record
        $match = UserMatch::where(function ($q) use ($user1Id, $user2Id) {
            $q->where('user_one_id', min($user1Id, $user2Id))
              ->where('user_two_id', max($user1Id, $user2Id));
        })->first();

        if ($match && !$match->conversation) {
            Conversation::create([
                'match_id' => $match->id,
            ]);
        }
    }
}

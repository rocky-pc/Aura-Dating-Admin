<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\UserMatch;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LikeController extends Controller
{
    /**
     * Send a like to another user
     */
    public function sendLike(Request $request)
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
}
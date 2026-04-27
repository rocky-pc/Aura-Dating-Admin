<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\UserMatch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MatchesController extends Controller
{
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
}
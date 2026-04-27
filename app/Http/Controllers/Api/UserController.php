<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserSwipe;
use App\Models\UserMatch;
use App\Models\Like;
use App\Models\UserReport;
use App\Models\UserBlock;
use App\Models\ProfileImage;
use App\Models\Hobby;
use App\Models\UserHobby;
use App\Models\Notification;
use App\Models\ConnectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Get users for discovery
     */
    public function discovery(Request $request)
    {
        $user = $request->user();
        
        // Get IDs that the user has already swiped
        $swipedIds = UserSwipe::where('swiper_id', $user->id)
            ->pluck('swiped_id')
            ->toArray();
        
        // Get IDs that the user has blocked
        $blockedIds = UserBlock::where('blocker_id', $user->id)
            ->pluck('blocked_id')
            ->toArray();
        
        // Get IDs that have blocked the user
        $blockedByIds = UserBlock::where('blocked_id', $user->id)
            ->pluck('blocker_id')
            ->toArray();
        
        $excludeIds = array_merge($swipedIds, $blockedIds, $blockedByIds, [$user->id]);
        
        // Build query
        $query = User::where('is_active', true)
            ->where('is_verified', true)
            ->whereNotIn('id', $excludeIds)
            ->with(['profile', 'images' => function ($q) {
                $q->orderBy('is_primary', 'desc')->orderBy('display_order');
            }]);
        
        // Apply age preference filter
        $profile = $user->profile;
        if ($profile) {
            if ($profile->min_age_preference) {
                $query->whereHas('profile', function ($q) use ($profile) {
                    $q->whereRaw('DATE_ADD(date_of_birth, INTERVAL ? YEAR) <= NOW()', [$profile->min_age_preference]);
                });
            }
            if ($profile->max_age_preference) {
                $query->whereHas('profile', function ($q) use ($profile) {
                    $q->whereRaw('DATE_ADD(date_of_birth, INTERVAL ? YEAR) >= NOW()', [$profile->max_age_preference]);
                });
            }
            // Gender preference
            if ($profile->show_gender && $profile->show_gender !== 'both') {
                $query->whereHas('profile', function ($q) use ($profile) {
                    $q->where('gender', $profile->show_gender);
                });
            }
        }
        
        $users = $query->paginate($request->get('per_page', 10));
        
        return response()->json($users);
    }

    /**
     * Get nearby users
     */
    public function nearby(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        
        if (!$profile || !$profile->location_lat || !$profile->location_lng) {
            return response()->json([
                'message' => 'Please set your location first',
            ], 400);
        }
        
        $maxDistance = $profile->max_distance_preference ?? 50; // Default 50km
        
        // Get nearby users using Haversine formula
        $nearbyUsers = User::where('is_active', true)
            ->where('is_verified', true)
            ->where('id', '!=', $user->id)
            ->whereHas('profile', function ($q) use ($profile, $maxDistance) {
                $q->select(DB::raw('*, (6371 * acos(cos(radians(?)) * cos(radians(location_lat)) * cos(radians(location_lng) - radians(?)) + sin(radians(?)) * sin(radians(location_lat)))) AS distance'))
                    ->setBinding([$profile->location_lat, $profile->location_lng, $profile->location_lat, $profile->location_lng])
                    ->having('distance', '<=', $maxDistance);
            })
            ->with(['profile', 'images' => function ($q) {
                $q->orderBy('is_primary', 'desc');
            }])
            ->orderBy('last_active_at', 'desc')
            ->paginate($request->get('per_page', 20));
        
        return response()->json($nearbyUsers);
    }

    /**
     * Get user profile
     */
    public function show(Request $request, $id)
    {
        $user = User::with(['profile', 'images', 'hobbies.hobby'])
            ->findOrFail($id);

        // Prevent viewing admin/moderator profiles
        if (in_array($user->role, ['admin', 'moderator'])) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Check if user is blocked
        $isBlocked = UserBlock::where('blocker_id', $request->user()->id)
            ->where('blocked_id', $id)
            ->exists();

        if ($isBlocked) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Get user stats
        $matchesCount = UserMatch::where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
        })->where('is_active', true)->count();

        $likesCount = Like::where('sender_id', $user->id)->count();

        $chatsCount = UserMatch::where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
        })->where('is_active', true)->whereHas('conversation')->count();

        return response()->json([
            'user' => $user,
            'stats' => [
                'matches' => $matchesCount,
                'likes' => $likesCount,
                'chats' => $chatsCount,
            ],
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Get allowed fields from request
        $allowedFields = [
            'first_name', 'last_name', 'bio', 'gender', 'date_of_birth',
            'job_title', 'company', 'school', 'show_gender',
            'min_age_preference', 'max_age_preference', 'max_distance_preference'
        ];

        $updateData = $request->only($allowedFields);

        // Debug: Log received data
        Log::info('ApiUserController - Profile update request data', [
            'user_id' => $user->id,
            'received_data' => $updateData,
            'all_request_data' => $request->all()
        ]);

        // Validate the data
        $validated = $request->validate([
            'first_name' => 'sometimes|nullable|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'bio' => 'sometimes|nullable|string|max:500',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'date_of_birth' => 'sometimes|nullable|date',
            'job_title' => 'sometimes|nullable|string|max:100',
            'company' => 'sometimes|nullable|string|max:100',
            'school' => 'sometimes|nullable|string|max:100',
            'show_gender' => 'sometimes|nullable|in:male,female,both,none',
            'min_age_preference' => 'sometimes|nullable|integer|min:18|max:100',
            'max_age_preference' => 'sometimes|nullable|integer|min:18|max:100',
            'max_distance_preference' => 'sometimes|nullable|integer|min:1|max:100',
        ]);

        Log::info('ApiUserController - Validated data', ['validated' => $validated]);

        // Ensure profile exists, create if not
        if (!$user->profile) {
            Log::info('ApiUserController - Creating new profile for user', ['user_id' => $user->id]);
            $user->profile()->create($validated);
        } else {
            Log::info('ApiUserController - Updating existing profile for user', ['user_id' => $user->id, 'profile_id' => $user->profile->id]);
            $user->profile()->update($validated);
        }

        $freshUser = $user->fresh(['profile', 'images']);
        Log::info('ApiUserController - Profile update result', ['updated_profile' => $freshUser->profile]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $freshUser,
            'updated_fields' => array_keys($validated), // Debug info
        ]);
    }

    /**
     * Like a user
     */
    public function like(Request $request, $id)
    {
        return $this->swipe($request, $id, 'like');
    }

    /**
     * Dislike a user
     */
    public function dislike(Request $request, $id)
    {
        return $this->swipe($request, $id, 'dislike');
    }

    /**
     * Super like a user
     */
    public function superLike(Request $request, $id)
    {
        return $this->swipe($request, $id, 'super_like');
    }

    /**
     * Handle swipe action
     */
    private function swipe(Request $request, $id, $action)
    {
        $swiper = $request->user();
        
        // Prevent self-swipe
        if ($swiper->id == $id) {
            return response()->json([
                'message' => 'Cannot swipe on yourself',
            ], 400);
        }
        
        // Check if user exists
        $swipedUser = User::findOrFail($id);

        // Prevent interaction with admin/moderator accounts
        if ($swipedUser->role !== 'user') {
            return response()->json([
                'message' => 'Cannot interact with this user',
            ], 403);
        }
        
        // Check if already swiped
        $existingSwipe = UserSwipe::where('swiper_id', $swiper->id)
            ->where('swiped_id', $id)
            ->first();
        
        if ($existingSwipe) {
            return response()->json([
                'message' => 'Already swiped on this user',
            ], 400);
        }
        
        // Create swipe record
        UserSwipe::create([
            'swiper_id' => $swiper->id,
            'swiped_id' => $id,
            'action' => $action,
        ]);
        
        $match = null;
        
        // Create like record for matching system
        if ($action === 'like' || $action === 'super_like') {
            // Check if there's already a like relationship
            $existingLike = Like::where(function ($q) use ($swiper, $id) {
                $q->where('sender_id', $swiper->id)->where('receiver_id', $id)
                  ->orWhere('sender_id', $id)->where('receiver_id', $swiper->id);
            })->first();

            if (!$existingLike) {
                // Create new like
                Like::create([
                    'sender_id' => $swiper->id,
                    'receiver_id' => $id,
                    'status' => 'pending'
                ]);

                // Send notification to receiver
                Notification::create([
                    'user_id' => $id,
                    'type' => 'like',
                    'title' => 'New Like!',
                    'body' => $swiper->profile->first_name . ' liked you!',
                    'data' => ['sender_id' => $swiper->id],
                ]);
            }
        }
        
        return response()->json([
            'message' => $action . ' recorded',
            'match' => $match ? $match->load('conversation') : null,
        ]);
    }

    /**
     * Report a user
     */
    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|in:fake_profile,inappropriate_behavior,harassment,spam,scam,other',
            'description' => 'sometimes|string|max:1000',
        ]);
        
        UserReport::create([
            'reporter_id' => $request->user()->id,
            'reported_id' => $id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);
        
        return response()->json([
            'message' => 'Report submitted successfully',
        ]);
    }

    /**
     * Block a user
     */
    public function block(Request $request, $id)
    {
        UserBlock::firstOrCreate([
            'blocker_id' => $request->user()->id,
            'blocked_id' => $id,
        ]);
        
        // Delete any existing matches
        UserMatch::where(function ($q) use ($request, $id) {
            $q->where(function ($q2) use ($request, $id) {
                $q2->where('user_one_id', $request->user()->id)->where('user_two_id', $id);
            })->orWhere(function ($q2) use ($request, $id) {
                $q2->where('user_one_id', $id)->where('user_two_id', $request->user()->id);
            });
        })->delete();
        
        return response()->json([
            'message' => 'User blocked successfully',
        ]);
    }

    /**
     * Upload profile image
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        $user = $request->user();
        
        $path = $request->file('image')->store('profile_images', 'public');
        
        $image = ProfileImage::create([
            'user_id' => $user->id,
            'image_url' => Storage::url($path),
            'is_primary' => $request->boolean('is_primary', false),
            'display_order' => $user->images()->count(),
        ]);
        
        // If this is primary, unset other primary images
        if ($image->is_primary) {
            $user->images()->where('id', '!=', $image->id)->update(['is_primary' => false]);
        }
        
        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => $image,
        ]);
    }

    /**
     * Delete profile image
     */
    public function deleteImage(Request $request, $id)
    {
        $image = ProfileImage::where('user_id', $request->user()->id)
            ->findOrFail($id);

        // Delete file
        $path = str_replace('/storage/', '', $image->image_url);
        Storage::disk('public')->delete($path);

        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully',
        ]);
    }

    /**
     * Set primary profile image
     */
    public function setPrimaryImage(Request $request, $id)
    {
        $user = $request->user();

        // Find the image
        $image = ProfileImage::where('user_id', $user->id)
            ->findOrFail($id);

        // Unset all other primary images for this user
        ProfileImage::where('user_id', $user->id)
            ->where('id', '!=', $id)
            ->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return response()->json([
            'message' => 'Primary image updated successfully',
            'image' => $image,
        ]);
    }

    /**
     * Get all hobbies
     */
    public function getHobbies()
    {
        $hobbies = Hobby::where('is_active', true)->get();
        
        return response()->json([
            'hobbies' => $hobbies,
        ]);
    }

    /**
     * Update user hobbies
     */
    public function updateHobbies(Request $request)
    {
        $request->validate([
            'hobbies' => 'required|array',
            'hobbies.*' => 'exists:hobbies,id',
        ]);
        
        $user = $request->user();
        
        // Delete existing hobbies
        $user->hobbies()->delete();
        
        // Add new hobbies
        foreach ($request->hobbies as $hobbyId) {
            UserHobby::create([
                'user_id' => $user->id,
                'hobby_id' => $hobbyId,
            ]);
        }
        
        return response()->json([
            'message' => 'Hobbies updated successfully',
            'hobbies' => $user->fresh(['hobbies.hobby'])->hobbies,
        ]);
    }

    /**
     * Get notifications
     */
    public function notifications(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));
        
        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);
        
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json([
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Update location
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        
        $user = $request->user();
        
        $user->profile()->update([
            'location_lat' => $request->latitude,
            'location_lng' => $request->longitude,
        ]);
        
        return response()->json([
            'message' => 'Location updated successfully',
        ]);
    }

    /**
     * Get discovery settings
     */
    public function getDiscoverySettings(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        
        if (!$profile) {
            return response()->json([
                'message' => 'Profile not found',
            ], 404);
        }
        
        return response()->json([
            'max_distance' => $profile->max_distance,
            'min_age' => $profile->min_age,
            'max_age' => $profile->max_age,
            'interested_in' => $profile->interested_in,
        ]);
    }

    /**
     * Update discovery settings
     */
    public function updateDiscoverySettings(Request $request)
    {
        $request->validate([
            'max_distance' => 'sometimes|integer|min:1|max:200',
            'min_age' => 'sometimes|integer|min:18|max:100',
            'max_age' => 'sometimes|integer|min:18|max:100',
            'interested_in' => 'sometimes|in:male,female,everyone',
        ]);
        
        $user = $request->user();
        
        $user->profile()->update($request->only([
            'max_distance',
            'min_age',
            'max_age',
            'interested_in',
        ]));
        
        return response()->json([
            'message' => 'Discovery settings updated successfully',
            'settings' => $user->profile->fresh()->only([
                'max_distance',
                'min_age',
                'max_age',
                'interested_in',
            ]),
        ]);
    }

    /**
     * Get connection status between current user and target user
     */
    public function getConnectionStatus(Request $request, $userId)
    {
        $currentUser = $request->user();
        
        $connection = ConnectionRequest::where(function ($q) use ($currentUser, $userId) {
            $q->where('sender_id', $currentUser->id)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($currentUser, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $currentUser->id);
        })->first();

        if (!$connection) {
            return response()->json([
                'status' => 'none',
            ]);
        }

        if ($connection->status === 'accepted') {
            return response()->json([
                'status' => 'accepted',
                'request_id' => $connection->id,
            ]);
        }

        if ($connection->status === 'pending') {
            if ($connection->sender_id === $currentUser->id) {
                return response()->json([
                    'status' => 'pending_sent',
                    'request_id' => $connection->id,
                ]);
            } else {
                return response()->json([
                    'status' => 'pending_received',
                    'request_id' => $connection->id,
                ]);
            }
        }

        return response()->json([
            'status' => 'none',
        ]);
    }

    /**
     * Send connection request to user
     */
    public function sendConnectionRequest(Request $request, $userId)
    {
        $currentUser = $request->user()->load('profile');

        if ($currentUser->id == $userId) {
            return response()->json([
                'message' => 'Cannot send connection request to yourself',
            ], 400);
        }

        $targetUser = User::findOrFail($userId);

        // Check existing request
        $existing = \App\Models\ConnectionRequest::where(function ($q) use ($currentUser, $userId) {
            $q->where('sender_id', $currentUser->id)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($currentUser, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $currentUser->id);
        })->first();

        if ($existing) {
            return response()->json([
                'message' => 'Connection request already exists',
            ], 400);
        }

        $connection = \App\Models\ConnectionRequest::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $userId,
            'status' => 'pending',
        ]);

        // Send notification
        $senderName = $currentUser->profile ? $currentUser->profile->first_name : $currentUser->email;
        Notification::create([
            'user_id' => $userId,
            'type' => 'connection_request',
            'title' => 'New Connection Request',
            'body' => $senderName . ' sent you a connection request',
            'data' => ['request_id' => $connection->id, 'sender_id' => $currentUser->id],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Connection request sent',
            'request' => $connection,
        ]);
    }

    /**
     * Accept connection request
     */
    public function acceptConnectionRequest(Request $request, $requestId)
    {
        $currentUser = $request->user();
        
        $connection = \App\Models\ConnectionRequest::where('id', $requestId)
            ->where('receiver_id', $currentUser->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $connection->update(['status' => 'accepted']);

        // Create match and conversation
        $match = UserMatch::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_one_id' => min($connection->sender_id, $connection->receiver_id),
            'user_two_id' => max($connection->sender_id, $connection->receiver_id),
            'is_active' => true,
        ]);

        $match->conversation()->create([
            'is_active' => true,
        ]);

        // Notify sender
        Notification::create([
            'user_id' => $connection->sender_id,
            'type' => 'connection_accepted',
            'title' => 'Connection Accepted!',
            'body' => $currentUser->profile->first_name . ' accepted your connection request',
            'data' => ['user_id' => $currentUser->id],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Connection request accepted',
            'match' => $match->load('conversation'),
        ]);
    }

    /**
     * Decline connection request
     */
    public function declineConnectionRequest(Request $request, $requestId)
    {
        $currentUser = $request->user();
        
        $connection = \App\Models\ConnectionRequest::where('id', $requestId)
            ->where('receiver_id', $currentUser->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $connection->update(['status' => 'declined']);

        return response()->json([
            'success' => true,
            'message' => 'Connection request declined',
        ]);
    }

    /**
     * Get all connection requests
     */
    public function getConnectionRequests(Request $request)
    {
        $currentUser = $request->user();
        
        $requests = \App\Models\ConnectionRequest::where(function ($q) use ($currentUser) {
            $q->where('sender_id', $currentUser->id)->orWhere('receiver_id', $currentUser->id);
        })->with('sender.profile', 'receiver.profile')->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json($requests);
    }

    /**
     * Get pending incoming connection requests
     */
    public function getPendingConnectionRequests(Request $request)
    {
        $currentUser = $request->user();
        
        $requests = \App\Models\ConnectionRequest::where('receiver_id', $currentUser->id)
            ->where('status', 'pending')
            ->with('sender.profile', 'sender.images')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'requests' => $requests,
        ]);
    }

    /**
     * Remove connection
     */
    public function removeConnection(Request $request, $userId)
    {
        $currentUser = $request->user();

        $connection = ConnectionRequest::where(function ($q) use ($currentUser, $userId) {
            $q->where('sender_id', $currentUser->id)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($currentUser, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $currentUser->id);
        })->first();

        if ($connection) {
            $connection->delete();
        }

        // Delete match
        UserMatch::where(function ($q) use ($currentUser, $userId) {
            $q->where('user_one_id', $currentUser->id)->where('user_two_id', $userId);
        })->orWhere(function ($q) use ($currentUser, $userId) {
            $q->where('user_one_id', $userId)->where('user_two_id', $currentUser->id);
        })->delete();

        return response()->json([
            'success' => true,
            'message' => 'Connection removed',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSwipe;
use App\Models\UserMatch;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ApiUserController extends Controller
{
    /**
     * Get discovery users - users that haven't been swiped yet
     */
    public function discovery(Request $request)
    {
        $user = Auth::user();
        
        // Get user's preferences
        $profile = $user->profile;
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your profile first'
            ], 400);
        }

        // Get users already swiped by this user
        $swipedUserIds = UserSwipe::where('swiper_id', $user->id)
            ->pluck('swiped_id')
            ->toArray();

        // Get blocked/blocked by users
        $blockedIds = DB::table('blocked_users')
            ->where('blocker_id', $user->id)
            ->orWhere('blocked_id', $user->id)
            ->pluck('blocker_id')
            ->pluck('blocked_id')
            ->toArray();

        // Build query for discovery users
        $query = User::where('id', '!=', $user->id)
            ->where('is_active', 1)
            ->where('role', 'user') // Only show regular users, exclude admin/moderator accounts
            ->whereNotIn('id', $swipedUserIds)
            ->whereNotIn('id', $blockedIds)
            ->with(['profile', 'images']);

        // Debug logging
        Log::info('Discovery query for user', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'total_users_found' => $query->count()
        ]);

        // Filter by gender preference
        if ($profile->interested_in && $profile->interested_in !== 'everyone') {
            $query->whereHas('profile', function ($q) use ($profile) {
                $q->where('gender', $profile->interested_in);
            });
        }

        // Filter by age range
        if ($profile->min_age) {
            $query->whereHas('profile', function ($q) use ($profile) {
                $q->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= ?', [$profile->min_age]);
            });
        }
        if ($profile->max_age && $profile->max_age < 100) {
            $query->whereHas('profile', function ($q) use ($profile) {
                $q->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= ?', [$profile->max_age]);
            });
        }

        // Location-based filtering (if lat/lng provided)
        if ($profile->latitude && $profile->longitude && $profile->max_distance) {
            // Haversine formula for distance calculation
            $query->whereHas('profile', function ($q) use ($profile) {
                $q->selectRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                    [$profile->latitude, $profile->longitude, $profile->latitude]
                )
                ->having('distance', '<=', $profile->max_distance);
            });
        }

        // Order by last active
        $query->orderByDesc('last_active_at');

        // Pagination
        $perPage = $request->input('per_page', 20);
        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Get nearby users (alternative discovery method)
     */
    public function nearby(Request $request)
    {
        return $this->discovery($request);
    }

    /**
     * Get user by UUID
     */
    public function show($uuid)
    {
        $user = User::where('uuid', $uuid)
            ->with(['profile', 'images'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Like a user (swipe right)
     */
    public function like($uuid)
    {
        return $this->processSwipe($uuid, 'like');
    }

    /**
     * Dislike a user (swipe left)
     */
    public function dislike($uuid)
    {
        return $this->processSwipe($uuid, 'pass');
    }

    /**
     * Super like a user (swipe up)
     */
    public function superLike($uuid)
    {
        return $this->processSwipe($uuid, 'super_like');
    }

    /**
     * Process swipe action
     */
    private function processSwipe($uuid, $action)
    {
        $swiper = Auth::user();
        
        // Find the user being swiped by UUID
        $swipedUser = User::where('uuid', $uuid)->first();
        
        if (!$swipedUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Can't swipe on yourself
        if ($swiper->id === $swipedUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot swipe on yourself'
            ], 400);
        }

        // Check if already swiped
        $existingSwipe = UserSwipe::where('swiper_id', $swiper->id)
            ->where('swiped_id', $swipedUser->id)
            ->first();

        if ($existingSwipe) {
            return response()->json([
                'success' => false,
                'message' => 'Already swiped on this user'
            ], 400);
        }

        // Create swipe record
        $swipe = UserSwipe::create([
            'swiper_id' => $swiper->id,
            'swiped_id' => $swipedUser->id,
            'action' => $action
        ]);

        // Check for match (if it's a like or super_like)
        $isMatch = false;
        $matchUuid = null;
        if (in_array($action, ['like', 'super_like'])) {
            // Check if the other user has already liked this user
            $otherSwipe = UserSwipe::where('swiper_id', $swipedUser->id)
                ->where('swiped_id', $swiper->id)
                ->whereIn('action', ['like', 'super_like'])
                ->first();

            if ($otherSwipe) {
                // Create match
                $match = UserMatch::create([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'user_one_id' => min($swiper->id, $swipedUser->id),
                    'user_two_id' => max($swiper->id, $swipedUser->id),
                    'user_one_super_like' => $action === 'super_like' ? 1 : 0,
                    'user_two_super_like' => $otherSwipe->action === 'super_like' ? 1 : 0
                ]);

                $isMatch = true;
                $matchUuid = $match->uuid;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'swipe' => $swipe,
                'is_match' => $isMatch,
                'match_id' => $matchUuid
            ]
        ]);
    }

    /**
     * Get connection status with a user
     */
    public function getConnectionStatus($uuid)
    {
        $user = Auth::user();
        $otherUser = User::where('uuid', $uuid)->first();

        if (!$otherUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if matched
        $match = UserMatch::where(function ($query) use ($user, $otherUser) {
            $query->where('user_one_id', $user->id)
                ->where('user_two_id', $otherUser->id);
        })->orWhere(function ($query) use ($user, $otherUser) {
            $query->where('user_one_id', $otherUser->id)
                ->where('user_two_id', $user->id);
        })->first();

        // Check swipe status
        $swipe = UserSwipe::where('swiper_id', $user->id)
            ->where('swiped_id', $otherUser->id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'is_matched' => $match ? true : false,
                'match_id' => $match?->uuid,
                'swipe_status' => $swipe?->action,
                'is_super_like' => $swipe?->action === 'super_like'
            ]
        ]);
    }

    /**
     * Report a user
     */
    public function report(Request $request, $uuid)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000'
        ]);

        $reporter = Auth::user();
        $reportedUser = User::where('uuid', $uuid)->first();

        if (!$reportedUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        DB::table('user_reports')->insert([
            'reporter_id' => $reporter->id,
            'reported_id' => $reportedUser->id,
            'reason' => $request->reason,
            'description' => $request->description,
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report submitted successfully'
        ]);
    }

    /**
     * Block a user
     */
    public function block($uuid)
    {
        $blocker = Auth::user();
        $blockedUser = User::where('uuid', $uuid)->first();

        if (!$blockedUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if already blocked
        $existingBlock = DB::table('blocked_users')
            ->where('blocker_id', $blocker->id)
            ->where('blocked_id', $blockedUser->id)
            ->first();

        if ($existingBlock) {
            return response()->json([
                'success' => false,
                'message' => 'User already blocked'
            ], 400);
        }

        DB::table('blocked_users')->insert([
            'blocker_id' => $blocker->id,
            'blocked_id' => $blockedUser->id,
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully'
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'date_of_birth' => 'sometimes|date|before:-18 years',
            'gender' => 'sometimes|in:male,female,non_binary,other',
            'interested_in' => 'sometimes|in:male,female,everyone',
            'bio' => 'sometimes|string|max:500',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'max_distance' => 'sometimes|integer|min:1|max:100',
            'min_age' => 'sometimes|integer|min:18|max:100',
            'max_age' => 'sometimes|integer|min:18|max:100'
        ]);

        if ($profile) {
            $profile->update($request->only([
                'first_name', 'last_name', 'date_of_birth', 'gender',
                'interested_in', 'bio', 'latitude', 'longitude',
                'max_distance', 'min_age', 'max_age'
            ]));
            
            // Mark profile as completed if all required fields are set
            if ($profile->first_name && $profile->date_of_birth && $profile->gender) {
                $profile->update(['profile_completed' => 1]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $profile->fresh()
        ]);
    }

    /**
     * Upload profile image
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_primary' => 'sometimes|boolean'
        ]);

        $user = Auth::user();

        // Handle file upload
        $path = $request->file('image')->store('profile_images', 'public');

        $image = DB::table('profile_images')->insertGetId([
            'user_id' => $user->id,
            'image_url' => '/storage/' . $path,
            'is_primary' => $request->input('is_primary', false) ? 1 : 0,
            'created_at' => now()
        ]);

        // If this is primary, unset other primary images
        if ($request->input('is_primary', false)) {
            DB::table('profile_images')
                ->where('user_id', $user->id)
                ->where('id', '!=', $image)
                ->update(['is_primary' => 0]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $image,
                'image_url' => '/storage/' . $path
            ]
        ]);
    }

    /**
     * Delete profile image
     */
    public function deleteImage($imageId)
    {
        $user = Auth::user();

        $image = DB::table('profile_images')
            ->where('id', $imageId)
            ->where('user_id', $user->id)
            ->first();

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        DB::table('profile_images')
            ->where('id', $imageId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    /**
     * Get available hobbies
     */
    public function getHobbies()
    {
        $hobbies = DB::table('hobbies')
            ->where('is_active', 1)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $hobbies
        ]);
    }

    /**
     * Update user hobbies
     */
    public function updateHobbies(Request $request)
    {
        $request->validate([
            'hobbies' => 'required|array',
            'hobbies.*' => 'integer|exists:hobbies,id'
        ]);

        $user = Auth::user();

        // Remove existing hobbies
        DB::table('user_hobbies')
            ->where('user_id', $user->id)
            ->delete();

        // Add new hobbies
        foreach ($request->hobbies as $hobbyId) {
            DB::table('user_hobbies')->insert([
                'user_id' => $user->id,
                'hobby_id' => $hobbyId
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hobbies updated successfully'
        ]);
    }

    /**
     * Get discovery settings
     */
    public function getDiscoverySettings()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return response()->json([
            'success' => true,
            'data' => [
                'max_distance' => $profile?->max_distance ?? 50,
                'min_age' => $profile?->min_age ?? 18,
                'max_age' => $profile?->max_age ?? 100,
                'interested_in' => $profile?->interested_in ?? 'everyone'
            ]
        ]);
    }

    /**
     * Update discovery settings
     */
    public function updateDiscoverySettings(Request $request)
    {
        $request->validate([
            'max_distance' => 'sometimes|integer|min:1|max:100',
            'min_age' => 'sometimes|integer|min:18|max:100',
            'max_age' => 'sometimes|integer|min:18|max:100',
            'interested_in' => 'sometimes|in:male,female,everyone'
        ]);

        $user = Auth::user();
        $profile = $user->profile;

        if ($profile) {
            $profile->update($request->only([
                'max_distance', 'min_age', 'max_age', 'interested_in'
            ]));
        }

        return response()->json([
            'success' => true,
            'data' => $profile->fresh()
        ]);
    }
}
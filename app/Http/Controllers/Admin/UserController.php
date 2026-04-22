<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserReport;
use App\Models\UserBlock;
use App\Models\ProfileImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List all users with pagination and filters
     */
    public function index(Request $request)
    {
        $query = User::with('profile')->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        if ($request->has('is_premium')) {
            $query->where('is_premium', $request->boolean('is_premium'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->paginate($request->get('per_page', 20));

        return response()->json($users);
    }

    /**
     * Web - List all users with pagination and filters
     */
    public function webIndex(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('verified')) {
            $query->where('is_verified', $request->verified === '1');
        }

        $users = $query->paginate(20);

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'premium' => User::where('is_premium', true)->count(),
            'verified' => User::where('is_verified', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Get single user details
     */
    public function show(User $user)
    {
        $user->load(['profile', 'images', 'hobbies.hobby', 'subscription']);
        
        // Get user reports
        $reports = UserReport::where('reported_id', $user->id)
            ->with('reporter')
            ->latest()
            ->take(10)
            ->get();
        
        // Get user blocks
        $blocks = UserBlock::where('blocked_id', $user->id)
            ->with('blocker')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'user' => $user,
            'reports' => $reports,
            'blocks' => $blocks,
        ]);
    }

    /**
     * Get user data for editing
     */
    public function editData(User $user)
    {
        $user->load('profile');
        
        $userData = $user->toArray();
        if ($user->profile && $user->profile->date_of_birth) {
            $userData['profile']['date_of_birth'] = $user->profile->date_of_birth->format('Y-m-d');
        }
        
        return response()->json([
            'user' => $userData,
        ]);
    }

    /**
     * Create new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'sometimes|unique:users,phone',
            'password' => 'required|string|min:6',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:-18 years',
            'bio' => 'sometimes|string|max:500',
            'role' => 'sometimes|in:user,admin,moderator',
            'is_verified' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'is_premium' => 'sometimes|boolean',
        ]);

        // Create user
        $user = User::create([
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'] ?? true,
            'is_verified' => $validated['is_verified'] ?? false,
            'is_premium' => $validated['is_premium'] ?? false,
            'role' => $validated['role'] ?? 'user',
        ]);

        // Create user profile
        $user->profile()->create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'bio' => $validated['bio'] ?? null,
        ]);

        return redirect()->back()->with('success', 'User created successfully');
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|unique:users,phone,' . $user->id,
            'is_active' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
            'is_premium' => 'sometimes|boolean',
            'role' => 'sometimes|in:user,admin,moderator',
        ]);

        $user->update($validated);

        // Update profile if provided
        if ($request->has('profile')) {
            $user->profile()->update($request->profile);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh(['profile']),
        ]);
    }

    /**
     * Web - Update user from admin panel
     */
    public function webUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|unique:users,phone,' . $user->id,
            'is_active' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
            'is_premium' => 'sometimes|boolean',
            'role' => 'sometimes|in:user,admin,moderator',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'bio' => 'nullable|string|max:500',
        ]);

        $userData = [
            'email' => $validated['email'] ?? $user->email,
            'phone' => $validated['phone'] ?? $user->phone,
            'role' => $validated['role'] ?? $user->role,
        ];

        // Handle checkbox fields properly - they are only in request when checked
        if ($request->has('is_active')) {
            $userData['is_active'] = $request->boolean('is_active');
        }
        if ($request->has('is_verified')) {
            $userData['is_verified'] = $request->boolean('is_verified');
        }
        if ($request->has('is_premium')) {
            $userData['is_premium'] = $request->boolean('is_premium');
        }

        $user->update($userData);

        $profileData = [];
        if ($request->filled('first_name')) {
            $profileData['first_name'] = $validated['first_name'];
        }
        if ($request->filled('last_name')) {
            $profileData['last_name'] = $validated['last_name'];
        }
        if ($request->filled('gender')) {
            $profileData['gender'] = $validated['gender'];
        }
        if ($request->filled('date_of_birth')) {
            $profileData['date_of_birth'] = $validated['date_of_birth'];
        }
        if ($request->filled('bio')) {
            $profileData['bio'] = $validated['bio'];
        }

        if (!empty($profileData)) {
            $user->load('profile');
            try {
                if ($user->profile) {
                    $user->profile()->update($profileData);
                } else {
                    $user->profile()->create(array_merge(['user_id' => $user->id], $profileData));
                }
            } catch (\Exception $e) {
                \Log::error('Profile update failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'User updated successfully');
    }

    /**
     * Verify user account
     */
    public function verify(User $user)
    {
        $user->update(['is_verified' => true]);

        return redirect()->back()->with('success', 'User verified successfully');
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->back()->with('success', $user->is_active ? 'User activated' : 'User deactivated');
    }

    /**
     * Block user
     */
    public function block(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'sometimes|string|max:500',
        ]);

        $user->update([
            'is_active' => false,
        ]);

        return response()->json([
            'message' => 'User blocked successfully',
            'user' => $user,
        ]);
    }

    /**
     * Unblock user
     */
    public function unblock(User $user)
    {
        $user->update(['is_active' => true]);

        return response()->json([
            'message' => 'User unblocked successfully',
            'user' => $user,
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Soft delete the user
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Get user statistics
     */
    public function stats()
    {
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'verified' => User::where('is_verified', true)->count(),
            'unverified' => User::where('is_verified', false)->count(),
            'premium' => User::where('is_premium', true)->count(),
            'free' => User::where('is_premium', false)->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_this_week' => User::whereDate('created_at', '>=', now()->startOfWeek())->count(),
            'new_this_month' => User::whereDate('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return response()->json($stats);
    }
    
    /**
     * List demo users
     */
    public function listDemoUsers()
    {
        $users = User::where('is_verified', true)
            ->where('role', 'user')
            ->with(['profile', 'images'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'users' => $users,
            'count' => $users->count(),
        ]);
    }
    
    /**
     * Create a new demo user
     */
    public function createDemoUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|unique:users,phone',
            'password' => 'required|string|min:6',
            'gender' => 'required|in:male,female,non_binary,other',
            'date_of_birth' => 'required|date|before:today|after:1900-01-01',
            'bio' => 'nullable|string|max:500',
            'job_title' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'image_url' => 'nullable|url',
        ]);

        // Create user
        $user = User::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
            'is_verified' => true,
            'is_premium' => true,
            'role' => 'user',
            'last_active_at' => now(),
        ]);

        // Create user profile
        $profile = UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'interested_in' => 'everyone',
            'bio' => $validated['bio'] ?? null,
            'latitude' => rand(2500, 4500) / 100, // Random US latitude
            'longitude' => rand(-12500, -6500) / 100, // Random US longitude
            'location_updated_at' => now(),
            'max_distance' => 50,
            'min_age' => 18,
            'max_age' => 50,
            'profile_completed' => true,
        ]);

        // Create profile image if URL provided
        if (!empty($validated['image_url'])) {
            ProfileImage::create([
                'user_id' => $user->id,
                'image_url' => $validated['image_url'],
                'is_primary' => true,
                'is_verified' => true,
                'order' => 0,
            ]);
        } else {
            // Use a default placeholder image
            $defaultImages = [
                'male' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
                'female' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop&crop=face',
                'other' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400&h=400&fit=crop&crop=face',
                'non_binary' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=400&h=400&fit=crop&crop=face',
            ];
            
            ProfileImage::create([
                'user_id' => $user->id,
                'image_url' => $defaultImages[$validated['gender']] ?? $defaultImages['other'],
                'is_primary' => true,
                'is_verified' => true,
                'order' => 0,
            ]);
        }

        $user->load(['profile', 'images']);

        return response()->json([
            'message' => 'Demo user created successfully',
            'user' => $user,
        ], 201);
    }
    
    /**
     * Delete a demo user
     */
    public function deleteDemoUser(User $user)
    {
        // Prevent deleting admin users
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Cannot delete admin users',
            ], 403);
        }

        // Soft delete the user
        $user->delete();

        return response()->json([
            'message' => 'Demo user deleted successfully',
        ]);
    }

}

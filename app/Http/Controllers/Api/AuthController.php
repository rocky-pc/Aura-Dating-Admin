<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use App\Models\Wallet;
use App\Models\WalletSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'phone' => 'sometimes|unique:users,phone',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'date_of_birth' => 'required|date|before:-18 years',
            'gender' => 'required|in:male,female,other',
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
            'role' => 'user',
        ]);

        // Create user profile
        $user->profile()->create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
        ]);

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Get wallet info (created automatically by User model)
        $wallet = $user->wallet;
        $signupBonus = $wallet ? $wallet->bonus_points : 0;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user->load('profile'),
            'wallet' => [
                'bonus_points' => $signupBonus,
                'balance' => $wallet ? $wallet->balance : 0,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email',
            'password' => 'required|string',
        ]);

        $credentials = $request->has('email')
            ? ['email' => $request->email, 'password' => $request->password]
            : ['phone' => $request->phone, 'password' => $request->password];

        $user = User::where('email', $request->email ?? null)
            ->orWhere('phone', $request->phone ?? null)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Account is disabled',
            ], 403);
        }

        // Update last active
        $user->update(['last_active_at' => now()]);

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('profile'),
            'token' => $token,
        ]);
    }

    /**
     * Send OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,phone',
            'email' => 'required_if:type,email|email',
            'phone' => 'required_if:type,phone',
        ]);

        // Rate limiting
        $key = 'otp:' . ($request->email ?? $request->phone);
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many attempts. Try again in {$seconds} seconds.",
            ], 429);
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Find or create user
        $user = User::firstOrCreate(
            [$request->type => $request->{$request->type}]
        );

        // Create or update OTP
        OtpVerification::updateOrCreate(
            ['user_id' => $user->id, 'type' => $request->type],
            [
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
                'is_verified' => false,
            ]
        );

        // In production, send OTP via email/SMS here
        // For development, return the OTP
        RateLimiter::hit($key, 300);

        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => $otp, // Remove in production
            'user_id' => $user->id,
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|string|size:6',
        ]);

        $otpRecord = OtpVerification::where('user_id', $request->user_id)
            ->where('is_verified', false)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'No pending OTP found',
            ], 404);
        }

        if (!$otpRecord->isValid()) {
            return response()->json([
                'message' => 'OTP expired',
            ], 400);
        }

        if (!Hash::check($request->otp, $otpRecord->otp)) {
            return response()->json([
                'message' => 'Invalid OTP',
            ], 400);
        }

        // Mark OTP as verified
        $otpRecord->update(['is_verified' => true]);

        // Mark user as verified
        $user = User::find($request->user_id);
        $user->update(['is_verified' => true]);

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Phone/Email verified successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load(['profile', 'images', 'hobbies.hobby', 'subscription']),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:50',
            'last_name' => 'sometimes|string|max:50',
            'bio' => 'sometimes|string|max:500',
            'gender' => 'sometimes|in:male,female,other',
            'date_of_birth' => 'sometimes|date|before:today|after:1900-01-01',
            'job_title' => 'sometimes|string|max:100',
            'company' => 'sometimes|string|max:100',
            'school' => 'sometimes|string|max:100',
            'show_gender' => 'sometimes|in:male,female,both,none',
            'min_age_preference' => 'sometimes|integer|min:18|max:100',
            'max_age_preference' => 'sometimes|integer|min:18|max:100',
            'max_distance_preference' => 'sometimes|integer|min:1|max:100',
        ]);

        $user->profile()->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(['profile']),
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Invalidate all other tokens
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Generate reset token
        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);

        // In production, send email with reset link here

        return response()->json([
            'message' => 'Password reset instructions sent to email',
            'token' => $token, // Remove in production
        ]);
    }
}

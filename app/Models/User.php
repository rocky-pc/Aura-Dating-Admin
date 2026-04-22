<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WalletSettings;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'uuid',
        'email',
        'phone',
        'password',
        'is_active',
        'is_verified',
        'is_premium',
        'premium_expires_at',
        'role',
        'last_active_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_premium' => 'boolean',
        'premium_expires_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = \Illuminate\Support\Str::uuid();
            }
        });

        static::created(function ($user) {
            // Only create wallet if one doesn't already exist
            if (!$user->wallet) {
                try {
                    $signupBonus = 50; // Default value
                    try {
                        $signupBonus = WalletSettings::getValue('signup_bonus_points', 50);
                    } catch (\Exception $e) {
                        // Use default if settings fail
                    }

                    $user->wallet()->create([
                        'balance' => 0,
                        'bonus_points' => $signupBonus,
                        'total_spent' => 0,
                        'lifetime_earnings' => 0,
                    ]);
                } catch (\Exception $e) {
                    // Log the error but don't fail user creation
                    \Log::error('Failed to create wallet for user ' . $user->id . ': ' . $e->getMessage());
                }
            }
        });
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProfileImage::class);
    }

    public function hobbies(): HasMany
    {
        return $this->hasMany(UserHobby::class);
    }

    public function swipes(): HasMany
    {
        return $this->hasMany(UserSwipe::class, 'swiper_id');
    }

    public function matchesAsUserOne(): HasMany
    {
        return $this->hasMany(UserMatch::class, 'user_one_id');
    }

    public function matchesAsUserTwo(): HasMany
    {
        return $this->hasMany(UserMatch::class, 'user_two_id');
    }

    public function getMatchesAttribute()
    {
        return UserMatch::where('user_one_id', $this->id)
            ->orWhere('user_two_id', $this->id)
            ->where('is_active', true)
            ->get();
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('is_active', true);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function sentLikes(): HasMany
    {
        return $this->hasMany(Like::class, 'sender_id');
    }

    public function receivedLikes(): HasMany
    {
        return $this->hasMany(Like::class, 'receiver_id');
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }
}

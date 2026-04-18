<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'balance',
        'bonus_points',
        'total_spent',
        'lifetime_earnings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'integer',
        'bonus_points' => 'integer',
        'total_spent' => 'integer',
        'lifetime_earnings' => 'integer',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add points to the wallet
     */
    public function addPoints(int $points, string $type = 'bonus'): void
    {
        if ($type === 'bonus') {
            $this->increment('bonus_points', $points);
        } else {
            $this->increment('balance', $points);
        }
    }

    /**
     * Deduct points from the wallet
     */
    public function deductPoints(int $points): bool
    {
        $totalAvailable = $this->balance + $this->bonus_points;
        
        if ($totalAvailable < $points) {
            return false;
        }

        // Deduct from bonus points first, then balance
        if ($this->bonus_points >= $points) {
            $this->decrement('bonus_points', $points);
        } else {
            $remaining = $points - $this->bonus_points;
            $this->bonus_points = 0;
            $this->decrement('balance', $remaining);
        }

        return true;
    }

    /**
     * Get total available points
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->balance + $this->bonus_points;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSwipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'swiper_id',
        'swiped_id',
        'action', // 'like', 'dislike', 'super_like', 'favorite'
        'is_match',
    ];

    protected $casts = [
        'is_match' => 'boolean',
    ];

    public function swiper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'swiper_id');
    }

    public function swiped(): BelongsTo
    {
        return $this->belongsTo(User::class, 'swiped_id');
    }

    // Aliases for admin panel
    public function fromUser(): BelongsTo
    {
        return $this->swiper();
    }

    public function toUser(): BelongsTo
    {
        return $this->swiped();
    }
}

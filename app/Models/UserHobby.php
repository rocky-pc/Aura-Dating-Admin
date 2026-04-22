<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHobby extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hobby_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hobby(): BelongsTo
    {
        return $this->belongsTo(Hobby::class);
    }
}

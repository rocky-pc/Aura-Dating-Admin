<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hobby extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userHobbies(): HasMany
    {
        return $this->hasMany(UserHobby::class);
    }
}

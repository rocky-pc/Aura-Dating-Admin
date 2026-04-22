<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'show_gender',
        'bio',
        'job_title',
        'company',
        'school',
        'location_lat',
        'location_lng',
        'city',
        'country',
        'min_age_preference',
        'max_age_preference',
        'max_distance_preference',
        'show_online_status',
        'show_distance',
        'is_complete',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'location_lat' => 'double',
        'location_lng' => 'double',
        'min_age_preference' => 'integer',
        'max_age_preference' => 'integer',
        'max_distance_preference' => 'integer',
        'show_online_status' => 'boolean',
        'show_distance' => 'boolean',
        'is_complete' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->age;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

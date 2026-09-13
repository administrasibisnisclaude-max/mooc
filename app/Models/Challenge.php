<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = ['title', 'description', 'type', 'difficulty', 'time_limit', 'points_per_level', 'icon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function levels()   { return $this->hasMany(ChallengeLevel::class)->orderBy('level_number'); }
    public function attempts() { return $this->hasMany(ChallengeAttempt::class); }

    public function bestAttemptBy(int $userId): ?ChallengeAttempt
    {
        return $this->attempts()->where('user_id', $userId)->orderByDesc('score')->first();
    }

    public function getDifficultyBadgeAttribute(): string
    {
        return match($this->difficulty) {
            'easy'   => 'success',
            'medium' => 'warning',
            'hard'   => 'danger',
            default  => 'secondary',
        };
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->levels()->count() * $this->points_per_level;
    }
}

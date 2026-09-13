<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeAttempt extends Model
{
    protected $fillable = ['user_id', 'challenge_id', 'score', 'levels_completed', 'time_taken', 'is_completed', 'started_at', 'completed_at'];

    protected $casts = ['is_completed' => 'boolean', 'started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function user()      { return $this->belongsTo(User::class); }
    public function challenge() { return $this->belongsTo(Challenge::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeLevel extends Model
{
    protected $fillable = ['challenge_id', 'level_number', 'title', 'instructions', 'puzzle_data', 'answer', 'hints'];

    protected $casts = ['puzzle_data' => 'array', 'hints' => 'array'];

    public function challenge() { return $this->belongsTo(Challenge::class); }
}

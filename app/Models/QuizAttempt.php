<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['user_id', 'quiz_id', 'score', 'started_at', 'submitted_at', 'passed'];
    protected $casts = ['started_at' => 'datetime', 'submitted_at' => 'datetime', 'passed' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function answers() { return $this->hasMany(QuizAnswer::class, 'attempt_id'); }
}

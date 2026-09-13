<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['course_id', 'section_id', 'title', 'description', 'passing_score', 'time_limit'];

    public function course() { return $this->belongsTo(Course::class); }
    public function section() { return $this->belongsTo(Section::class); }
    public function questions() { return $this->hasMany(QuizQuestion::class)->orderBy('order'); }
    public function attempts() { return $this->hasMany(QuizAttempt::class); }
}

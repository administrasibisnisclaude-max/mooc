<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['course_id', 'title', 'order'];

    public function course() { return $this->belongsTo(Course::class); }
    public function lessons() { return $this->hasMany(Lesson::class)->orderBy('order'); }
    public function quizzes() { return $this->hasMany(Quiz::class); }
    public function assignments() { return $this->hasMany(Assignment::class); }
}

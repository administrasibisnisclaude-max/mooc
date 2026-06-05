<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['course_id', 'section_id', 'title', 'description', 'due_date', 'max_score', 'file_requirements'];
    protected $casts = ['due_date' => 'datetime'];

    public function course() { return $this->belongsTo(Course::class); }
    public function section() { return $this->belongsTo(Section::class); }
    public function submissions() { return $this->hasMany(AssignmentSubmission::class); }
}

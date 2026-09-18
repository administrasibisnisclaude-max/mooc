<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['course_id', 'tutor_id', 'title', 'content'];

    public function course() { return $this->belongsTo(Course::class); }
    public function tutor() { return $this->belongsTo(User::class, 'tutor_id'); }
}

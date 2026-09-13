<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    protected $fillable = ['course_id', 'user_id', 'title', 'content', 'is_pinned'];
    protected $casts = ['is_pinned' => 'boolean'];

    public function course() { return $this->belongsTo(Course::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function replies() { return $this->hasMany(ForumReply::class, 'thread_id'); }
}

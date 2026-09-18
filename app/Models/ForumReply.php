<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    protected $fillable = ['thread_id', 'user_id', 'content', 'is_answer'];
    protected $casts = ['is_answer' => 'boolean'];

    public function thread() { return $this->belongsTo(ForumThread::class, 'thread_id'); }
    public function user() { return $this->belongsTo(User::class); }
}

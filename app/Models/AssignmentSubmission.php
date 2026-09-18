<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = ['assignment_id', 'user_id', 'file_path', 'notes', 'submitted_at', 'score', 'feedback', 'graded_at'];
    protected $casts = ['submitted_at' => 'datetime', 'graded_at' => 'datetime'];

    public function assignment() { return $this->belongsTo(Assignment::class); }
    public function user() { return $this->belongsTo(User::class); }
}

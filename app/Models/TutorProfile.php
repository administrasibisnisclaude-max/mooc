<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorProfile extends Model
{
    protected $fillable = ['user_id', 'expertise', 'social_links', 'verification_status', 'verified_at', 'rejection_reason'];

    protected $casts = ['social_links' => 'array', 'verified_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}

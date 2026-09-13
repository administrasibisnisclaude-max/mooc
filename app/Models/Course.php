<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'thumbnail', 'tutor_id', 'category_id',
        'price', 'level', 'status', 'language', 'requirements', 'what_youll_learn', 'rejection_reason'
    ];

    public function tutor() { return $this->belongsTo(User::class, 'tutor_id'); }
    public function category() { return $this->belongsTo(Category::class); }
    public function sections() { return $this->hasMany(Section::class)->orderBy('order'); }
    public function lessons() { return $this->hasManyThrough(Lesson::class, Section::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }
    public function students() { return $this->belongsToMany(User::class, 'enrollments')->withPivot('progress_percentage', 'enrolled_at'); }
    public function quizzes() { return $this->hasMany(Quiz::class); }
    public function assignments() { return $this->hasMany(Assignment::class); }
    public function announcements() { return $this->hasMany(Announcement::class); }
    public function forumThreads() { return $this->hasMany(ForumThread::class); }
    public function certificates() { return $this->hasMany(Certificate::class); }
    public function reviews() { return $this->hasMany(CourseReview::class); }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getFormattedPriceAttribute()
    {
        return $this->price == 0 ? 'Gratis' : 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'bio', 'is_verified',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isTutor(): bool { return $this->role === 'tutor'; }
    public function isStudent(): bool { return $this->role === 'student'; }

    public function tutorProfile() { return $this->hasOne(TutorProfile::class); }
    public function courses() { return $this->hasMany(Course::class, 'tutor_id'); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }
    public function enrolledCourses() { return $this->belongsToMany(Course::class, 'enrollments')->withPivot('progress_percentage', 'enrolled_at', 'completed_at'); }
    public function lessonProgress() { return $this->hasMany(LessonProgress::class); }
    public function quizAttempts() { return $this->hasMany(QuizAttempt::class); }
    public function assignmentSubmissions() { return $this->hasMany(AssignmentSubmission::class); }
    public function forumThreads() { return $this->hasMany(ForumThread::class); }
    public function forumReplies() { return $this->hasMany(ForumReply::class); }
    public function certificates() { return $this->hasMany(Certificate::class); }
    public function courseReviews() { return $this->hasMany(CourseReview::class); }
    public function announcements() { return $this->hasMany(Announcement::class, 'tutor_id'); }
}

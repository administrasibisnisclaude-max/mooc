<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'section_id', 'title', 'type', 'content', 'video_url',
        'duration', 'order', 'is_free_preview', 'quiz_id',
    ];

    protected $casts = ['is_free_preview' => 'boolean'];

    public function section()   { return $this->belongsTo(Section::class); }
    public function quiz()      { return $this->belongsTo(Quiz::class); }
    public function materials() { return $this->hasMany(Material::class); }
    public function progress()  { return $this->hasMany(LessonProgress::class); }

    public function isCompletedBy(User $user): bool
    {
        return $this->progress()->where('user_id', $user->id)->whereNotNull('completed_at')->exists();
    }

    public function getFormattedDurationAttribute(): string
    {
        $m = intdiv($this->duration, 60);
        $s = $this->duration % 60;
        return sprintf('%d:%02d', $m, $s);
    }
}

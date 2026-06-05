<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['lesson_id', 'title', 'file_path', 'file_type', 'file_size'];

    public function lesson() { return $this->belongsTo(Lesson::class); }

    public function getFormattedSizeAttribute()
    {
        if (!$this->file_size) return 'Unknown';
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 1) . ' MB';
    }
}

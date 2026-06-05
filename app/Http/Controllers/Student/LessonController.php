<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        $user = Auth::user();
        $course = $lesson->section->course;

        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();
        $lesson->load('materials');

        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['watch_time' => 0]
        );

        $prevLesson = null;
        $nextLesson = null;
        $allLessons = $course->sections->flatMap->lessons->sortBy(fn($l) => [$l->section->order, $l->order]);
        $keys = $allLessons->keys()->values();
        $currentKey = $keys->search(fn($k) => $allLessons[$k]->id === $lesson->id);

        if ($currentKey > 0) $prevLesson = $allLessons[$keys[$currentKey - 1]];
        if ($currentKey < $keys->count() - 1) $nextLesson = $allLessons[$keys[$currentKey + 1]];

        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->whereHas('lesson', fn($q) => $q->whereHas('section', fn($q2) => $q2->where('course_id', $course->id)))
            ->pluck('lesson_id')
            ->toArray();

        return view('student.lesson', compact('lesson', 'course', 'enrollment', 'progress', 'prevLesson', 'nextLesson', 'completedLessonIds'));
    }

    public function markComplete(Lesson $lesson, Request $request)
    {
        $user = Auth::user();
        $course = $lesson->section->course;
        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        // update course progress
        $totalLessons = $course->sections->flatMap->lessons->count();
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->whereHas('lesson', fn($q) => $q->whereHas('section', fn($q2) => $q2->where('course_id', $course->id)))
            ->count();

        $pct = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        $enrollment->update([
            'progress_percentage' => $pct,
            'completed_at' => $pct >= 100 ? now() : null,
        ]);

        if ($request->wantsJson()) {
            // Determine next lesson URL
            $allLessons = $course->sections->flatMap->lessons->sortBy(fn($l) => [$l->section->order, $l->order])->values();
            $currentIdx = $allLessons->search(fn($l) => $l->id === $lesson->id);
            $nextUrl = ($currentIdx !== false && $currentIdx < $allLessons->count() - 1)
                ? route('student.lesson', $allLessons[$currentIdx + 1])
                : null;

            return response()->json([
                'success' => true,
                'progress' => round($pct),
                'next_url' => $nextUrl,
            ]);
        }

        return back()->with('success', 'Pelajaran ditandai selesai!');
    }
}

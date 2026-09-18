<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class StudentMonitoringController extends Controller
{
    public function index(Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $enrollments = Enrollment::with('user')
            ->where('course_id', $course->id)
            ->paginate(20);
        return view('tutor.students.index', compact('course', 'enrollments'));
    }

    public function show(Course $course, $userId)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $enrollment = Enrollment::with(['user', 'course'])
            ->where('course_id', $course->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $progress = \App\Models\LessonProgress::where('user_id', $userId)
            ->whereHas('lesson', fn($q) => $q->whereHas('section', fn($q2) => $q2->where('course_id', $course->id)))
            ->get();

        return view('tutor.students.show', compact('course', 'enrollment', 'progress'));
    }
}

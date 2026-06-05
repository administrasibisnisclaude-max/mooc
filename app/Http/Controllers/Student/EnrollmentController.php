<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        $user = Auth::user();
        $existing = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists();
        if ($existing) {
            return back()->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        return redirect()->route('student.learn', $course)->with('success', 'Berhasil mendaftar kursus!');
    }

    public function myCourses()
    {
        $enrollments = Auth::user()->enrollments()->with('course.tutor', 'course.category')->latest()->paginate(12);
        return view('student.courses', compact('enrollments'));
    }

    public function learn(Course $course)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();
        $course->load(['sections.lessons.materials', 'sections.quizzes', 'sections.assignments', 'announcements.tutor']);

        $completedLessonIds = $user->lessonProgress()
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->toArray();

        return view('student.learn', compact('course', 'enrollment', 'completedLessonIds'));
    }
}

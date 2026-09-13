<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(Course $course)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Only allow review if course is completed
        abort_if($enrollment->progress_percentage < 100, 403, 'Selesaikan kursus terlebih dahulu untuk memberikan ulasan.');

        $existing = CourseReview::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return view('student.review', compact('course', 'existing'));
    }

    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        abort_if($enrollment->progress_percentage < 100, 403);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        CourseReview::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['rating' => $request->rating, 'review' => $request->review]
        );

        return redirect()->route('courses.detail', $course->slug)
            ->with('success', 'Ulasan Anda berhasil disimpan. Terima kasih!');
    }

    public function destroy(Course $course)
    {
        CourseReview::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}

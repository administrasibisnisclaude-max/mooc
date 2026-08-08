<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $courses = Course::with(['tutor', 'category'])
            ->where('status', $status)
            ->paginate(15);
        return view('admin.courses.index', compact('courses', 'status'));
    }

    public function show(Course $course)
    {
        $course->load(['tutor', 'category', 'sections.lessons']);
        return view('admin.courses.show', compact('course'));
    }

    public function approve(Course $course)
    {
        $course->update(['status' => 'published']);
        return back()->with('success', 'Course berhasil dipublish.');
    }

    public function reject(Request $request, Course $course)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $course->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        return back()->with('success', 'Course ditolak.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('courses')->whereNull('parent_id')->get();
        $featuredCourses = Course::with(['tutor', 'category', 'reviews'])
            ->where('status', 'published')
            ->latest()
            ->take(8)
            ->get();
        $totalStudents = \App\Models\User::where('role', 'student')->count();
        $totalCourses = Course::where('status', 'published')->count();
        return view('home', compact('categories', 'featuredCourses', 'totalStudents', 'totalCourses'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isTutor()) return redirect()->route('tutor.dashboard');
        return redirect()->route('student.dashboard');
    }

    public function courses(Request $request)
    {
        $query = Course::with(['tutor', 'category', 'reviews'])->where('status', 'published');

        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->level) {
            $query->where('level', $request->level);
        }

        $courses = $query->latest()->paginate(12);
        $categories = Category::all();
        return view('courses.index', compact('courses', 'categories'));
    }

    public function courseDetail($slug)
    {
        $course = Course::with(['tutor', 'category', 'sections.lessons', 'reviews.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $isEnrolled = false;
        $enrollment = null;
        if (Auth::check()) {
            $enrollment = $course->enrollments()->where('user_id', Auth::id())->first();
            $isEnrolled = $enrollment !== null;
        }

        $totalLessons = $course->sections->flatMap->lessons->count();
        $totalDuration = $course->sections->flatMap->lessons->sum('duration');

        return view('courses.detail', compact('course', 'isEnrolled', 'enrollment', 'totalLessons', 'totalDuration'));
    }
}

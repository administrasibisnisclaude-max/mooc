<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Auth::user()->courses()->with('category')->withCount('enrollments')->paginate(10);
        return view('tutor.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('tutor.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced',
            'language' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('thumbnail');
        $data['tutor_id'] = Auth::id();
        $data['slug'] = Str::slug($request->title) . '-' . time();
        $data['status'] = 'draft';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = Course::create($data);
        return redirect()->route('tutor.courses.show', $course)->with('success', 'Course berhasil dibuat.');
    }

    public function show(Course $course)
    {
        $this->authorizeOwner($course);
        $course->load(['sections.lessons', 'enrollments.user']);
        return view('tutor.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorizeOwner($course);
        $categories = Category::all();
        return view('tutor.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeOwner($course);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced',
        ]);

        $data = $request->except('thumbnail');
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update($data);
        return redirect()->route('tutor.courses.show', $course)->with('success', 'Course berhasil diperbarui.');
    }

    public function submitForReview(Course $course)
    {
        $this->authorizeOwner($course);
        $course->update(['status' => 'pending']);
        return back()->with('success', 'Course telah dikirim untuk review admin.');
    }

    public function destroy(Course $course)
    {
        $this->authorizeOwner($course);
        $course->delete();
        return redirect()->route('tutor.courses.index')->with('success', 'Course berhasil dihapus.');
    }

    private function authorizeOwner(Course $course)
    {
        if ($course->tutor_id !== Auth::id()) {
            abort(403);
        }
    }
}

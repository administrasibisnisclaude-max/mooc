<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $assignments = $course->assignments()->with('section')->withCount('submissions')->get();
        return view('tutor.assignments.index', compact('course', 'assignments'));
    }

    public function create(Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $sections = $course->sections;
        return view('tutor.assignments.create', compact('course', 'sections'));
    }

    public function store(Request $request, Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'max_score' => 'required|integer|min:1',
        ]);

        $course->assignments()->create($request->only('title', 'description', 'due_date', 'max_score', 'file_requirements', 'section_id'));
        return redirect()->route('tutor.assignments.index', $course)->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit(Course $course, Assignment $assignment)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $sections = $course->sections;
        return view('tutor.assignments.edit', compact('course', 'assignment', 'sections'));
    }

    public function update(Request $request, Course $course, Assignment $assignment)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $request->validate(['title' => 'required|string|max:255', 'description' => 'required|string']);
        $assignment->update($request->only('title', 'description', 'due_date', 'max_score', 'file_requirements', 'section_id'));
        return redirect()->route('tutor.assignments.index', $course)->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $assignment->delete();
        return redirect()->route('tutor.assignments.index', $course)->with('success', 'Tugas berhasil dihapus.');
    }
}

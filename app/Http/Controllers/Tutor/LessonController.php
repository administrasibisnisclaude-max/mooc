<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function createSection(Request $request, Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $request->validate(['title' => 'required|string|max:255']);
        $order = $course->sections()->max('order') + 1;
        $course->sections()->create(['title' => $request->title, 'order' => $order]);
        return back()->with('success', 'Bagian berhasil ditambahkan.');
    }

    public function destroySection(Section $section)
    {
        abort_if($section->course->tutor_id !== Auth::id(), 403);
        $section->delete();
        return back()->with('success', 'Bagian berhasil dihapus.');
    }

    public function create(Section $section)
    {
        abort_if($section->course->tutor_id !== Auth::id(), 403);
        return view('tutor.lessons.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        abort_if($section->course->tutor_id !== Auth::id(), 403);
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,document,text',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string',
            'duration' => 'nullable|integer',
            'is_free_preview' => 'nullable|boolean',
        ]);

        $order = $section->lessons()->max('order') + 1;
        $section->lessons()->create([
            'title' => $request->title,
            'type' => $request->type,
            'content' => $request->content,
            'video_url' => $request->video_url,
            'duration' => $request->duration ?? 0,
            'order' => $order,
            'is_free_preview' => $request->boolean('is_free_preview'),
        ]);

        return redirect()->route('tutor.courses.show', $section->course_id)->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Lesson $lesson)
    {
        abort_if($lesson->section->course->tutor_id !== Auth::id(), 403);
        return view('tutor.lessons.edit', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        abort_if($lesson->section->course->tutor_id !== Auth::id(), 403);
        $request->validate(['title' => 'required|string|max:255']);
        $lesson->update($request->only('title', 'type', 'content', 'video_url', 'duration', 'is_free_preview'));
        return redirect()->route('tutor.courses.show', $lesson->section->course_id)->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Lesson $lesson)
    {
        abort_if($lesson->section->course->tutor_id !== Auth::id(), 403);
        $courseId = $lesson->section->course_id;
        $lesson->delete();
        return redirect()->route('tutor.courses.show', $courseId)->with('success', 'Materi berhasil dihapus.');
    }
}

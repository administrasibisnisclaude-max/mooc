<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $announcements = $course->announcements()->latest()->paginate(10);
        return view('tutor.announcements.index', compact('course', 'announcements'));
    }

    public function store(Request $request, Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $request->validate(['title' => 'required|string', 'content' => 'required|string']);
        $course->announcements()->create([
            'tutor_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
        return back()->with('success', 'Pengumuman berhasil dikirim.');
    }

    public function destroy(Course $course, Announcement $announcement)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $announcement->delete();
        return back()->with('success', 'Pengumuman dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(Course $course)
    {
        $user = Auth::user();
        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();
        $threads = $course->forumThreads()->with(['user', 'replies'])->orderByDesc('is_pinned')->latest()->paginate(15);
        return view('student.forum.index', compact('course', 'threads'));
    }

    public function store(Request $request, Course $course)
    {
        Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->firstOrFail();
        $request->validate(['title' => 'required|string|max:255', 'content' => 'required|string']);
        $thread = $course->forumThreads()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
        return redirect()->route('student.forum.show', [$course, $thread]);
    }

    public function show(Course $course, ForumThread $thread)
    {
        Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->firstOrFail();
        $thread->load('user', 'replies.user');
        return view('student.forum.show', compact('course', 'thread'));
    }

    public function reply(Request $request, Course $course, ForumThread $thread)
    {
        Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->firstOrFail();
        $request->validate(['content' => 'required|string']);
        $thread->replies()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);
        return back()->with('success', 'Balasan berhasil ditambahkan.');
    }

    public function markAnswer(ForumReply $reply)
    {
        $thread = $reply->thread;
        abort_if($thread->user_id !== Auth::id() && $thread->course->enrollments()->where('user_id', Auth::id())->doesntExist(), 403);
        $reply->update(['is_answer' => !$reply->is_answer]);
        return back();
    }
}

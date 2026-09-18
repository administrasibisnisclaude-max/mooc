<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradingController extends Controller
{
    public function index(Course $course, Assignment $assignment)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $submissions = $assignment->submissions()->with('user')->paginate(20);
        return view('tutor.grading.index', compact('course', 'assignment', 'submissions'));
    }

    public function grade(Request $request, AssignmentSubmission $submission)
    {
        abort_if($submission->assignment->course->tutor_id !== Auth::id(), 403);
        $request->validate([
            'score' => 'required|numeric|min:0',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Nilai berhasil disimpan.');
    }
}

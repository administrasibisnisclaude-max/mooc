<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentSubmissionController extends Controller
{
    public function show(Assignment $assignment)
    {
        $user = Auth::user();
        Enrollment::where('user_id', $user->id)->where('course_id', $assignment->course_id)->firstOrFail();
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', $user->id)->first();
        return view('student.assignment', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        Enrollment::where('user_id', $user->id)->where('course_id', $assignment->course_id)->firstOrFail();

        $request->validate([
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $data = [
            'assignment_id' => $assignment->id,
            'user_id' => $user->id,
            'notes' => $request->notes,
            'submitted_at' => now(),
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('submissions', 'public');
        }

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => $user->id],
            $data
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}

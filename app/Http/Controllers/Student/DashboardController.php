<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()->with('course.tutor')->latest()->get();
        $stats = [
            'total_enrolled' => $enrollments->count(),
            'completed' => $enrollments->whereNotNull('completed_at')->count(),
            'in_progress' => $enrollments->whereNull('completed_at')->count(),
            'certificates' => $user->certificates()->count(),
        ];
        $recentEnrollments = $enrollments->take(5);
        return view('student.dashboard', compact('stats', 'recentEnrollments'));
    }
}

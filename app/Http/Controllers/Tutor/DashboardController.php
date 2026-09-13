<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $courses = $user->courses()->withCount('enrollments')->get();
        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => $courses->sum('enrollments_count'),
            'published_courses' => $courses->where('status', 'published')->count(),
            'pending_courses' => $courses->where('status', 'pending')->count(),
        ];
        $recentEnrollments = \App\Models\Enrollment::with(['user', 'course'])
            ->whereHas('course', fn($q) => $q->where('tutor_id', $user->id))
            ->latest()
            ->take(5)
            ->get();
        return view('tutor.dashboard', compact('stats', 'courses', 'recentEnrollments'));
    }
}

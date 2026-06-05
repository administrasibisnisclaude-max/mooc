<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_tutors' => User::where('role', 'tutor')->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::where('status', 'published')->count(),
            'pending_courses' => Course::where('status', 'pending')->count(),
            'total_enrollments' => Enrollment::count(),
            'total_certificates' => Certificate::count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentCourses = Course::with('tutor')->latest()->take(5)->get();
        $pendingTutors = User::where('role', 'tutor')
            ->whereHas('tutorProfile', fn($q) => $q->where('verification_status', 'pending'))
            ->count();
        $stats['pending_tutors'] = $pendingTutors;

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentCourses'));
    }
}

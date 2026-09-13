<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;

class ReportController extends Controller
{
    public function index()
    {
        $monthlyEnrollments = Enrollment::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        $topCourses = Course::withCount('enrollments')
            ->where('status', 'published')
            ->orderByDesc('enrollments_count')
            ->take(10)
            ->get();

        $stats = [
            'total_revenue' => Enrollment::join('courses', 'enrollments.course_id', '=', 'courses.id')->sum('courses.price'),
            'total_completions' => Enrollment::whereNotNull('completed_at')->count(),
            'avg_progress' => Enrollment::avg('progress_percentage'),
        ];

        return view('admin.reports.index', compact('monthlyEnrollments', 'topCourses', 'stats'));
    }
}

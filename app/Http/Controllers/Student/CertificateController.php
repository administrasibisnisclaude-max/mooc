<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Auth::user()->certificates()->with('course')->get();
        return view('student.certificates', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        abort_if($certificate->user_id !== Auth::id(), 403);
        $certificate->load('user', 'course.tutor');
        return view('student.certificate-view', compact('certificate'));
    }
}

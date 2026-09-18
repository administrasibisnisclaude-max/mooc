<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course']);
        if ($request->search) {
            $query->where('certificate_number', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }
        $certificates = $query->latest()->paginate(15);
        return view('admin.certificates.index', compact('certificates'));
    }

    public function issue(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $enrollment = Enrollment::with(['user', 'course'])->findOrFail($request->enrollment_id);

        $existing = Certificate::where('user_id', $enrollment->user_id)
            ->where('course_id', $enrollment->course_id)
            ->exists();

        if ($existing) {
            return back()->with('error', 'Sertifikat sudah diterbitkan.');
        }

        Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'certificate_number' => 'CERT-' . strtoupper(uniqid()),
            'issued_at' => now(),
        ]);

        return back()->with('success', 'Sertifikat berhasil diterbitkan.');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();
        return back()->with('success', 'Sertifikat dihapus.');
    }
}

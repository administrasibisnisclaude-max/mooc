<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TutorProfile;
use Illuminate\Http\Request;

class TutorVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $tutors = User::with('tutorProfile')
            ->where('role', 'tutor')
            ->whereHas('tutorProfile', fn($q) => $q->where('verification_status', $status))
            ->paginate(15);
        return view('admin.tutors.index', compact('tutors', 'status'));
    }

    public function show(User $user)
    {
        $user->load('tutorProfile', 'courses');
        return view('admin.tutors.show', compact('user'));
    }

    public function approve(User $user)
    {
        $user->tutorProfile->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);
        $user->update(['is_verified' => true]);
        return back()->with('success', 'Tutor berhasil diverifikasi.');
    }

    public function reject(Request $request, User $user)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $user->tutorProfile->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Tutor ditolak.');
    }
}

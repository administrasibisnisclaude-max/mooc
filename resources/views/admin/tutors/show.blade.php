@extends('layouts.admin')
@section('title', 'Detail Tutor')
@section('page-title', 'Detail Tutor')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;font-size:2rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold">{{ $user->name }}</h5>
                <p class="text-muted">{{ $user->email }}</p>
                <span class="badge {{ $user->tutorProfile?->verification_status === 'approved' ? 'bg-success' : ($user->tutorProfile?->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                    {{ ucfirst($user->tutorProfile?->verification_status) }}
                </span>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Keahlian</h6>
                <p class="text-muted">{{ $user->tutorProfile?->expertise ?? 'Belum diisi' }}</p>
                <h6 class="fw-bold mb-2 mt-3">Kursus Dibuat</h6>
                <p class="text-muted">{{ $user->courses->count() }} kursus</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        @if($user->tutorProfile?->verification_status === 'pending')
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Tindakan Verifikasi</h6>
                <form action="{{ route('admin.tutors.approve', $user) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success me-2">
                        <i class="bi bi-check-circle me-1"></i>Approve Tutor
                    </button>
                </form>

                <button class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#rejectForm">
                    <i class="bi bi-x-circle me-1"></i>Tolak Tutor
                </button>

                <div class="collapse mt-3" id="rejectForm">
                    <form action="{{ route('admin.tutors.reject', $user) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Alasan Penolakan</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm">Kirim Penolakan</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @if($user->tutorProfile?->rejection_reason)
        <div class="alert alert-danger">
            <strong>Alasan Penolakan:</strong> {{ $user->tutorProfile->rejection_reason }}
        </div>
        @endif

        <div class="card">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0">Kursus Dibuat</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th class="px-3">Judul</th><th>Status</th><th>Dibuat</th></tr>
                    </thead>
                    <tbody>
                        @forelse($user->courses as $course)
                        <tr>
                            <td class="px-3">{{ $course->title }}</td>
                            <td><span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'pending' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($course->status) }}</span></td>
                            <td class="text-muted small">{{ $course->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada kursus.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

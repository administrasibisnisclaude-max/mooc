@extends('layouts.tutor')
@section('title', 'Dashboard Tutor')
@section('page-title', 'Dashboard Tutor')

@section('content')
@if(auth()->user()->tutorProfile?->verification_status !== 'approved')
<div class="alert alert-warning d-flex align-items-center gap-3">
    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
    <div>
        <strong>Akun Anda belum terverifikasi.</strong>
        Status: {{ ucfirst(auth()->user()->tutorProfile?->verification_status ?? 'pending') }}. Kursus Anda tidak akan dipublish hingga akun terverifikasi oleh admin.
    </div>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-primary fs-2 mb-1">{{ $stats['total_courses'] }}</div>
            <p class="text-muted small mb-0">Total Kursus</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-success fs-2 mb-1">{{ $stats['total_students'] }}</div>
            <p class="text-muted small mb-0">Total Pelajar</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-info fs-2 mb-1">{{ $stats['published_courses'] }}</div>
            <p class="text-muted small mb-0">Kursus Published</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="text-warning fs-2 mb-1">{{ $stats['pending_courses'] }}</div>
            <p class="text-muted small mb-0">Menunggu Review</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Kursus Saya</h6>
                <a href="{{ route('tutor.courses.create') }}" class="btn btn-primary btn-sm">+ Buat Kursus</a>
            </div>
            <div class="card-body p-0">
                @forelse($courses->take(5) as $course)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="flex-grow-1">
                        <strong class="small">{{ Str::limit($course->title, 40) }}</strong>
                        <p class="mb-0 text-muted" style="font-size:11px;">{{ $course->enrollments_count }} pelajar</p>
                    </div>
                    <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'pending' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($course->status) }}</span>
                    <a href="{{ route('tutor.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">Kelola</a>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <p>Belum ada kursus.</p>
                    <a href="{{ route('tutor.courses.create') }}" class="btn btn-primary btn-sm">Buat Kursus Pertama</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Pendaftaran Terbaru</h6></div>
            <div class="card-body p-0">
                @forelse($recentEnrollments as $enrollment)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:35px;height:35px;font-size:13px;">
                        {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <strong class="small">{{ $enrollment->user->name }}</strong>
                        <p class="mb-0 text-muted" style="font-size:11px;">{{ Str::limit($enrollment->course->title, 35) }}</p>
                    </div>
                    <small class="text-muted">{{ $enrollment->created_at->diffForHumans() }}</small>
                </div>
                @empty
                <div class="text-center py-4 text-muted">Belum ada pendaftaran.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

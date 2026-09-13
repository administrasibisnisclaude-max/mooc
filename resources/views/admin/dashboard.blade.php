@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">Total User</p>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_users']) }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="bi bi-people-fill text-primary fs-4"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">
                <span class="text-success">{{ $stats['total_students'] }}</span> pelajar,
                <span class="text-info">{{ $stats['total_tutors'] }}</span> tutor
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">Total Kursus</p>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_courses']) }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="bi bi-book-fill text-success fs-4"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">
                <span class="text-success">{{ $stats['published_courses'] }}</span> published,
                <span class="text-warning">{{ $stats['pending_courses'] }}</span> pending
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">Total Pendaftaran</p>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_enrollments']) }}</h3>
                </div>
                <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="bi bi-person-check-fill text-warning fs-4"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Pelajar terdaftar di kursus</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">Sertifikat</p>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_certificates']) }}</h3>
                </div>
                <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="bi bi-award-fill text-danger fs-4"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Sertifikat diterbitkan</div>
        </div>
    </div>
</div>

@if($stats['pending_courses'] > 0 || $stats['pending_tutors'] > 0)
<div class="row g-3 mb-4">
    @if($stats['pending_courses'] > 0)
    <div class="col-md-6">
        <div class="alert alert-warning d-flex align-items-center gap-3 rounded-3">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            <div>
                <strong>{{ $stats['pending_courses'] }} kursus</strong> menunggu verifikasi
                <div><a href="{{ route('admin.courses.index') }}" class="alert-link">Tinjau sekarang &rarr;</a></div>
            </div>
        </div>
    </div>
    @endif
    @if($stats['pending_tutors'] > 0)
    <div class="col-md-6">
        <div class="alert alert-info d-flex align-items-center gap-3 rounded-3">
            <i class="bi bi-person-exclamation fs-4"></i>
            <div>
                <strong>{{ $stats['pending_tutors'] }} tutor</strong> menunggu verifikasi
                <div><a href="{{ route('admin.tutors.index') }}" class="alert-link">Tinjau sekarang &rarr;</a></div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white border-0 pb-0">
                <h6 class="fw-bold mb-0">User Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        @foreach($recentUsers as $user)
                        <tr>
                            <td class="px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:35px;height:35px;font-size:13px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong class="small">{{ $user->name }}</strong>
                                        <p class="mb-0 text-muted" style="font-size:11px;">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'tutor' ? 'bg-success' : 'bg-primary') }} bg-opacity-15 {{ $user->role === 'admin' ? 'text-danger' : ($user->role === 'tutor' ? 'text-success' : 'text-primary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="align-middle text-muted small">{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="{{ route('admin.users.index') }}" class="small text-primary">Lihat semua user &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white border-0 pb-0">
                <h6 class="fw-bold mb-0">Kursus Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        @foreach($recentCourses as $course)
                        <tr>
                            <td class="px-3">
                                <strong class="small">{{ Str::limit($course->title, 35) }}</strong>
                                <p class="mb-0 text-muted" style="font-size:11px;">by {{ $course->tutor->name }}</p>
                            </td>
                            <td class="align-middle">
                                <span class="badge {{ $course->status === 'published' ? 'bg-success' : ($course->status === 'pending' ? 'bg-warning text-dark' : ($course->status === 'rejected' ? 'bg-danger' : 'bg-secondary')) }} bg-opacity-15 {{ $course->status === 'published' ? 'text-success' : ($course->status === 'pending' ? 'text-warning' : ($course->status === 'rejected' ? 'text-danger' : 'text-secondary')) }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="{{ route('admin.courses.index') }}" class="small text-primary">Lihat semua kursus &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection

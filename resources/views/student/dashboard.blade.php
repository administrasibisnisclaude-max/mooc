@extends('layouts.app')
@section('title', 'Dashboard Pelajar')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <h4 class="fw-bold mb-0">Halo, {{ auth()->user()->name }}! 👋</h4>
        <p class="mb-0 opacity-75">Lanjutkan perjalanan belajarmu</p>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-0 shadow-sm">
                <div class="text-primary fs-2 fw-bold">{{ $stats['total_enrolled'] }}</div>
                <p class="text-muted small mb-0">Terdaftar</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-0 shadow-sm">
                <div class="text-warning fs-2 fw-bold">{{ $stats['in_progress'] }}</div>
                <p class="text-muted small mb-0">Sedang Belajar</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-0 shadow-sm">
                <div class="text-success fs-2 fw-bold">{{ $stats['completed'] }}</div>
                <p class="text-muted small mb-0">Selesai</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-0 shadow-sm">
                <div class="text-danger fs-2 fw-bold">{{ $stats['certificates'] }}</div>
                <p class="text-muted small mb-0">Sertifikat</p>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Kursus Saya</h5>
        <a href="{{ route('student.courses') }}" class="text-primary text-decoration-none small">Lihat Semua &rarr;</a>
    </div>

    @if($recentEnrollments->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-book fs-1 text-muted"></i>
        <p class="text-muted mt-2">Anda belum mendaftar kursus apapun.</p>
        <a href="{{ route('courses.index') }}" class="btn btn-primary">Jelajahi Kursus</a>
    </div>
    @else
    <div class="row g-4">
        @foreach($recentEnrollments as $enrollment)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-1">{{ Str::limit($enrollment->course->title, 50) }}</h6>
                    <p class="text-muted small mb-2">{{ $enrollment->course->tutor->name }}</p>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Progress</span>
                        <span>{{ round($enrollment->progress_percentage) }}%</span>
                    </div>
                    <div class="progress mb-3" style="height:6px;">
                        <div class="progress-bar" style="width:{{ $enrollment->progress_percentage }}%"></div>
                    </div>
                    <a href="{{ route('student.learn', $enrollment->course) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-play-fill me-1"></i>Lanjutkan Belajar
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="mt-4 text-center">
        <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">Temukan Kursus Baru</a>
        <a href="{{ route('student.certificates') }}" class="btn btn-outline-success ms-2">
            <i class="bi bi-award me-1"></i>Sertifikat Saya
        </a>
    </div>
</div>
@endsection

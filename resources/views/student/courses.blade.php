@extends('layouts.app')
@section('title', 'Kursus Saya')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <h4 class="fw-bold mb-0">Kursus Saya</h4>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        @forelse($enrollments as $enrollment)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                @if($enrollment->course->thumbnail)
                    <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" class="card-img-top" style="height:150px;object-fit:cover;" alt="">
                @else
                    <div class="bg-primary d-flex align-items-center justify-content-center text-white" style="height:150px;">
                        <i class="bi bi-book fs-1"></i>
                    </div>
                @endif
                <div class="card-body">
                    <span class="badge bg-primary bg-opacity-10 text-primary small mb-2">{{ $enrollment->course->category?->name ?? 'Umum' }}</span>
                    <h6 class="fw-bold mb-1">{{ Str::limit($enrollment->course->title, 50) }}</h6>
                    <p class="text-muted small mb-3">{{ $enrollment->course->tutor->name }}</p>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Progress</span>
                        <span class="fw-semibold">{{ round($enrollment->progress_percentage) }}%</span>
                    </div>
                    <div class="progress mb-3" style="height:8px;">
                        <div class="progress-bar" style="width:{{ $enrollment->progress_percentage }}%"></div>
                    </div>
                    @if($enrollment->completed_at)
                    <span class="badge bg-success mb-2">Selesai</span>
                    @endif
                    <div class="d-flex gap-2">
                        <a href="{{ route('student.learn', $enrollment->course) }}" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-play-fill me-1"></i>Lanjutkan
                        </a>
                        <a href="{{ route('student.forum.index', $enrollment->course) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-chat-dots"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-book fs-1 text-muted"></i>
            <p class="text-muted mt-2">Anda belum mendaftar kursus.</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary">Jelajahi Kursus</a>
        </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $enrollments->links() }}</div>
</div>
@endsection

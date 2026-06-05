@extends('layouts.tutor')
@section('title', 'Kursus Saya')
@section('page-title', 'Kursus Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">{{ $courses->total() }} kursus</p>
    <a href="{{ route('tutor.courses.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i>Buat Kursus Baru</a>
</div>

<div class="row g-4">
    @forelse($courses as $course)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">{{ Str::limit($course->title, 50) }}</h6>
                    <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'pending' ? 'warning text-dark' : ($course->status === 'rejected' ? 'danger' : 'secondary')) }}">{{ ucfirst($course->status) }}</span>
                </div>
                <p class="text-muted small mb-3">{{ Str::limit($course->description, 100) }}</p>
                <div class="d-flex gap-3 text-muted small mb-3">
                    <span><i class="bi bi-people me-1"></i>{{ $course->enrollments_count }} pelajar</span>
                    <span><i class="bi bi-tag me-1"></i>{{ $course->category?->name ?? 'Umum' }}</span>
                    <span><i class="bi bi-cash me-1"></i>{{ $course->formatted_price }}</span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('tutor.courses.show', $course) }}" class="btn btn-primary btn-sm">Kelola</a>
                    <a href="{{ route('tutor.courses.edit', $course) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                    <a href="{{ route('tutor.quizzes.index', $course) }}" class="btn btn-outline-info btn-sm">Quiz</a>
                    <a href="{{ route('tutor.assignments.index', $course) }}" class="btn btn-outline-warning btn-sm">Tugas</a>
                    @if($course->status === 'draft')
                    <form action="{{ route('tutor.courses.submit', $course) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Submit Review</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-book fs-1 text-muted"></i>
        <p class="text-muted mt-2">Anda belum memiliki kursus.</p>
        <a href="{{ route('tutor.courses.create') }}" class="btn btn-primary">Buat Kursus Pertama</a>
    </div>
    @endforelse
</div>
<div class="mt-4">{{ $courses->links() }}</div>
@endsection

@extends('layouts.tutor')
@section('title', 'Quiz')
@section('page-title', 'Kelola Quiz')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('tutor.courses.show', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali ke {{ Str::limit($course->title, 40) }}</a>
    </div>
    <a href="{{ route('tutor.quizzes.create', $course) }}" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Buat Quiz</a>
</div>

<div class="row g-4">
    @forelse($quizzes as $quiz)
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-2">{{ $quiz->title }}</h6>
                <p class="text-muted small mb-3">{{ $quiz->description }}</p>
                <div class="d-flex gap-3 text-muted small mb-3">
                    <span><i class="bi bi-question-circle me-1"></i>{{ $quiz->questions->count() }} pertanyaan</span>
                    <span><i class="bi bi-trophy me-1"></i>Passing: {{ $quiz->passing_score }}%</span>
                    @if($quiz->time_limit)<span><i class="bi bi-clock me-1"></i>{{ $quiz->time_limit }} menit</span>@endif
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tutor.quizzes.edit', [$course, $quiz]) }}" class="btn btn-primary btn-sm">Kelola Soal</a>
                    <form action="{{ route('tutor.quizzes.destroy', [$course, $quiz]) }}" method="POST" onsubmit="return confirm('Hapus quiz ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
        <p>Belum ada quiz.</p>
        <a href="{{ route('tutor.quizzes.create', $course) }}" class="btn btn-primary">Buat Quiz</a>
    </div>
    @endforelse
</div>
@endsection

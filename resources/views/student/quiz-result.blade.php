@extends('layouts.app')
@section('title', 'Hasil Quiz')

@section('content')
<div class="container py-5" style="max-width:700px;">
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            @if($attempt->passed)
            <div class="text-success mb-3"><i class="bi bi-check-circle-fill" style="font-size:5rem;"></i></div>
            <h3 class="fw-bold text-success">Selamat! Anda Lulus!</h3>
            @else
            <div class="text-warning mb-3"><i class="bi bi-x-circle-fill" style="font-size:5rem;"></i></div>
            <h3 class="fw-bold text-warning">Belum Lulus</h3>
            @endif

            <div class="row g-3 my-4 justify-content-center">
                <div class="col-4">
                    <div class="bg-light rounded p-3">
                        <h2 class="fw-bold text-primary mb-0">{{ round($attempt->score) }}%</h2>
                        <small class="text-muted">Nilai Anda</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-light rounded p-3">
                        <h2 class="fw-bold text-secondary mb-0">{{ $attempt->quiz->passing_score }}%</h2>
                        <small class="text-muted">Passing Score</small>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('student.learn', $attempt->quiz->course) }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Kursus
                </a>
                @if(!$attempt->passed)
                <a href="{{ route('student.quiz.start', $attempt->quiz) }}" class="btn btn-outline-primary">
                    Coba Lagi
                </a>
                @endif
            </div>
        </div>

        <div class="card-footer bg-white">
            <h6 class="fw-bold mb-3">Review Jawaban</h6>
            @foreach($attempt->answers as $answer)
            <div class="mb-3 p-3 rounded {{ $answer->option?->is_correct ? 'bg-success bg-opacity-5 border border-success' : 'bg-danger bg-opacity-5 border border-danger' }}">
                <p class="fw-semibold mb-2 small">{{ $answer->question->question }}</p>
                <p class="mb-1 small">
                    <strong>Jawaban Anda:</strong>
                    <span class="{{ $answer->option?->is_correct ? 'text-success' : 'text-danger' }}">
                        {{ $answer->option?->option_text ?? 'Tidak dijawab' }}
                        <i class="bi bi-{{ $answer->option?->is_correct ? 'check-circle' : 'x-circle' }} ms-1"></i>
                    </span>
                </p>
                @if(!$answer->option?->is_correct)
                <p class="mb-0 small">
                    <strong>Jawaban Benar:</strong>
                    <span class="text-success">{{ $answer->question->options->where('is_correct', true)->first()?->option_text }}</span>
                </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

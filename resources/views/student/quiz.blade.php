@extends('layouts.app')
@section('title', $quiz->title)

@section('content')
<div class="container py-4" style="max-width:800px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">{{ $quiz->title }}</h5>
                @if($quiz->time_limit)
                <span id="timer" class="badge bg-warning text-dark fs-6">{{ $quiz->time_limit }}:00</span>
                @endif
            </div>
            @if($quiz->description)
            <p class="mb-0 mt-1 opacity-75 small">{{ $quiz->description }}</p>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('student.quiz.submit', $attempt) }}" method="POST" id="quizForm">
                @csrf
                @foreach($quiz->questions as $i => $question)
                <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <p class="fw-semibold mb-3">{{ $i+1 }}. {{ $question->question }}</p>
                    <div class="row g-2">
                        @foreach($question->options as $option)
                        <div class="col-12">
                            <label class="d-flex align-items-center gap-3 p-3 border rounded option-label" style="cursor:pointer;" for="opt{{ $option->id }}">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" id="opt{{ $option->id }}" class="form-check-input mt-0">
                                {{ $option->option_text }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <span class="text-muted small">{{ $quiz->questions->count() }} soal &bull; Passing score: {{ $quiz->passing_score }}%</span>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin mengumpulkan jawaban?')">
                        <i class="bi bi-send me-1"></i>Kumpulkan Jawaban
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.option-label:hover { background: #f0f7ff; border-color: #0056D2 !important; }
input[type=radio]:checked + .option-label, .option-label:has(input:checked) { background: #e8f0fe; border-color: #0056D2 !important; }
</style>
@endpush

@if($quiz->time_limit)
@push('scripts')
<script>
let minutes = {{ $quiz->time_limit }};
let seconds = 0;
const timer = document.getElementById('timer');
const interval = setInterval(() => {
    seconds--;
    if (seconds < 0) { minutes--; seconds = 59; }
    if (minutes < 0) { clearInterval(interval); document.getElementById('quizForm').submit(); return; }
    timer.textContent = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
    if (minutes < 2) timer.className = 'badge bg-danger fs-6';
}, 1000);
</script>
@endpush
@endif
@endsection

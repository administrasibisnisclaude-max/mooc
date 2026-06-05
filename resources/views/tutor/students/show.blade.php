@extends('layouts.tutor')
@section('title', 'Detail Pelajar')
@section('page-title', 'Detail Pelajar')

@section('content')
<div class="mb-4">
    <a href="{{ route('tutor.students.index', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;font-size:1.8rem;">
                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold">{{ $enrollment->user->name }}</h5>
                <p class="text-muted small">{{ $enrollment->user->email }}</p>
                <div class="mt-3">
                    <strong class="fs-4 text-primary">{{ round($enrollment->progress_percentage) }}%</strong>
                    <p class="text-muted small mb-1">Progress</p>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" style="width:{{ $enrollment->progress_percentage }}%"></div>
                    </div>
                </div>
                @if($enrollment->completed_at)
                <span class="badge bg-success mt-2">Selesai {{ $enrollment->completed_at->format('d M Y') }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Progress Materi</h6></div>
            <div class="card-body p-0">
                @foreach($course->sections as $section)
                <div class="border-bottom px-3 py-2">
                    <strong class="small">{{ $section->title }}</strong>
                    @foreach($section->lessons as $lesson)
                    @php $completed = $progress->where('lesson_id', $lesson->id)->where('completed_at', '!=', null)->count() > 0; @endphp
                    <div class="d-flex align-items-center gap-2 ms-3 mt-1 small">
                        <i class="bi {{ $completed ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
                        <span class="{{ $completed ? '' : 'text-muted' }}">{{ $lesson->title }}</span>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', $assignment->title)

@section('content')
<div class="container py-5" style="max-width:800px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('student.learn', $assignment->course) }}">Kursus</a></li>
            <li class="breadcrumb-item active">Tugas</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning bg-opacity-10">
            <h5 class="fw-bold mb-1"><i class="bi bi-clipboard2-check text-warning me-2"></i>{{ $assignment->title }}</h5>
            @if($assignment->due_date)
            <p class="mb-0 small text-muted">Deadline: <strong>{{ $assignment->due_date->format('d M Y H:i') }}</strong></p>
            @endif
        </div>
        <div class="card-body">
            <div class="prose mb-3">{!! nl2br(e($assignment->description)) !!}</div>
            <dl class="row small">
                <dt class="col-4 text-muted">Nilai Maksimum</dt><dd class="col-8">{{ $assignment->max_score }}</dd>
                @if($assignment->file_requirements)
                <dt class="col-4 text-muted">Ketentuan File</dt><dd class="col-8">{{ $assignment->file_requirements }}</dd>
                @endif
            </dl>
        </div>
    </div>

    @if($submission && $submission->graded_at)
    <div class="alert alert-success mb-4">
        <h6 class="fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Tugas Sudah Dinilai</h6>
        <p class="mb-1">Nilai: <strong>{{ $submission->score }}/{{ $assignment->max_score }}</strong></p>
        @if($submission->feedback)
        <p class="mb-0">Feedback: {{ $submission->feedback }}</p>
        @endif
    </div>
    @endif

    @if(!$submission || !$submission->graded_at)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0">{{ $submission ? 'Update Submission' : 'Submit Tugas' }}</h6>
        </div>
        <div class="card-body">
            @if($submission)
            <div class="alert alert-info">
                <strong>Sudah dikumpulkan</strong> pada {{ $submission->submitted_at?->format('d M Y H:i') }}
                @if($submission->notes)<p class="mb-0 small">Catatan: {{ $submission->notes }}</p>@endif
            </div>
            @endif
            <form action="{{ route('student.assignment.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload File</label>
                    <input type="file" name="file" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Tuliskan catatan atau penjelasan...">{{ $submission?->notes }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>{{ $submission ? 'Update' : 'Kumpulkan' }} Tugas
                </button>
                <a href="{{ route('student.learn', $assignment->course) }}" class="btn btn-outline-secondary ms-2">Kembali</a>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

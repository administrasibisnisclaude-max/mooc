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

    {{-- ── Flash success after submit ─────────────────────────── --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible border-0 shadow-sm d-flex align-items-center gap-3 mb-4" role="alert">
        <div class="d-flex align-items-center justify-content-center bg-success rounded-circle text-white flex-shrink-0"
             style="width:44px;height:44px;">
            <i class="bi bi-check-lg fs-5"></i>
        </div>
        <div>
            <strong>Berhasil!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Assignment detail card ──────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header {{ $submission ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clipboard2-check {{ $submission ? 'text-success' : 'text-warning' }} me-2"></i>
                    {{ $assignment->title }}
                </h5>
                {{-- Status badge --}}
                @if($submission && $submission->graded_at)
                    <span class="badge bg-success fs-6 py-2 px-3">
                        <i class="bi bi-patch-check-fill me-1"></i>Sudah Dinilai
                    </span>
                @elseif($submission)
                    <span class="badge bg-primary fs-6 py-2 px-3">
                        <i class="bi bi-clock-history me-1"></i>Menunggu Penilaian
                    </span>
                @else
                    <span class="badge bg-warning text-dark fs-6 py-2 px-3">
                        <i class="bi bi-exclamation-circle me-1"></i>Belum Dikumpulkan
                    </span>
                @endif
            </div>
            @if($assignment->due_date)
            <p class="mb-0 mt-1 small text-muted">
                Deadline: <strong>{{ $assignment->due_date->format('d M Y H:i') }}</strong>
                @if(now()->gt($assignment->due_date) && !$submission)
                    <span class="badge bg-danger ms-2">Terlambat</span>
                @endif
            </p>
            @endif
        </div>
        <div class="card-body">
            <div class="prose mb-3">{!! nl2br(e($assignment->description)) !!}</div>
            <dl class="row small mb-0">
                <dt class="col-5 col-md-4 text-muted">Nilai Maksimum</dt>
                <dd class="col-7 col-md-8 fw-semibold">{{ $assignment->max_score }} poin</dd>
                @if($assignment->file_requirements)
                <dt class="col-5 col-md-4 text-muted">Ketentuan File</dt>
                <dd class="col-7 col-md-8">{{ $assignment->file_requirements }}</dd>
                @endif
            </dl>
        </div>
    </div>

    {{-- ── Status timeline ────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center gap-0">

                {{-- Step 1: Submitted --}}
                <div class="d-flex flex-column align-items-center" style="min-width:90px;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border-2
                                {{ $submission ? 'bg-success border-success text-white' : 'bg-white border text-muted' }}"
                         style="width:38px;height:38px;border-style:solid;border-width:2px;">
                        <i class="bi {{ $submission ? 'bi-check-lg' : 'bi-upload' }}" style="font-size:16px;"></i>
                    </div>
                    <span class="text-center mt-1" style="font-size:11px;color:{{ $submission ? '#198754' : '#6c757d' }};">
                        <strong>Dikumpulkan</strong>
                        @if($submission)
                        <br>{{ $submission->submitted_at?->format('d M') }}
                        @endif
                    </span>
                </div>

                {{-- Connector --}}
                <div class="flex-grow-1 border-top border-2 {{ $submission ? 'border-success' : 'border-secondary border-opacity-25' }}"
                     style="height:2px;margin-bottom:22px;"></div>

                {{-- Step 2: Under review --}}
                <div class="d-flex flex-column align-items-center" style="min-width:90px;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border-2
                                {{ $submission && $submission->graded_at ? 'bg-success border-success text-white' : ($submission ? 'bg-primary border-primary text-white' : 'bg-white border text-muted') }}"
                         style="width:38px;height:38px;border-style:solid;border-width:2px;">
                        <i class="bi {{ $submission ? 'bi-eye' : 'bi-eye' }}" style="font-size:16px;"></i>
                    </div>
                    <span class="text-center mt-1" style="font-size:11px;color:{{ $submission ? '#0d6efd' : '#6c757d' }};">
                        <strong>Diperiksa</strong>
                    </span>
                </div>

                {{-- Connector --}}
                <div class="flex-grow-1 border-top border-2 {{ $submission && $submission->graded_at ? 'border-success' : 'border-secondary border-opacity-25' }}"
                     style="height:2px;margin-bottom:22px;"></div>

                {{-- Step 3: Graded --}}
                <div class="d-flex flex-column align-items-center" style="min-width:90px;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle border-2
                                {{ $submission && $submission->graded_at ? 'bg-success border-success text-white' : 'bg-white border text-muted' }}"
                         style="width:38px;height:38px;border-style:solid;border-width:2px;">
                        <i class="bi bi-patch-check" style="font-size:16px;"></i>
                    </div>
                    <span class="text-center mt-1" style="font-size:11px;color:{{ $submission && $submission->graded_at ? '#198754' : '#6c757d' }};">
                        <strong>Dinilai</strong>
                        @if($submission && $submission->graded_at)
                        <br>{{ $submission->graded_at->format('d M') }}
                        @endif
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Graded result ───────────────────────────────────────── --}}
    @if($submission && $submission->graded_at)
    <div class="card border-0 shadow-sm mb-4 border-start border-success border-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center justify-content-center bg-success rounded-circle text-white"
                     style="width:50px;height:50px;min-width:50px;">
                    <i class="bi bi-trophy-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Tugas Sudah Dinilai</h6>
                    <span class="text-muted small">{{ $submission->graded_at->format('d M Y H:i') }}</span>
                </div>
                <div class="ms-auto text-end">
                    <span class="display-6 fw-bold text-success">{{ $submission->score }}</span>
                    <span class="text-muted">/{{ $assignment->max_score }}</span>
                </div>
            </div>
            @if($submission->feedback)
            <div class="bg-light rounded p-3">
                <p class="small fw-semibold text-muted mb-1"><i class="bi bi-chat-quote me-1"></i>Feedback Tutor</p>
                <p class="mb-0">{{ $submission->feedback }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Submission area ────────────────────────────────────── --}}
    @if($submission && !$submission->graded_at)
    {{-- Already submitted, waiting for grade --}}
    <div class="card border-0 shadow-sm border-start border-primary border-4 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle text-white"
                     style="width:44px;height:44px;min-width:44px;">
                    <i class="bi bi-hourglass-split fs-6"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Tugas Sudah Dikumpulkan</h6>
                    <span class="text-muted small">Dikumpulkan {{ $submission->submitted_at?->format('d M Y H:i') }}</span>
                </div>
            </div>
            @if($submission->file_path)
            <div class="d-flex align-items-center gap-2 mt-2 p-2 bg-light rounded">
                <i class="bi bi-file-earmark text-primary"></i>
                <span class="small flex-grow-1">{{ basename($submission->file_path) }}</span>
                <a href="{{ asset('storage/'.$submission->file_path) }}" class="btn btn-sm btn-outline-primary" download>
                    <i class="bi bi-download"></i>
                </a>
            </div>
            @endif
            @if($submission->notes)
            <p class="small text-muted mt-2 mb-0"><i class="bi bi-chat-left-text me-1"></i>{{ $submission->notes }}</p>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Submit / Re-submit form ──────────────────────────── --}}
    @if(!$submission || !$submission->graded_at)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex align-items-center gap-2">
            <i class="bi bi-send text-primary"></i>
            <h6 class="fw-bold mb-0">{{ $submission ? 'Perbarui Pengumpulan' : 'Kumpulkan Tugas' }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('student.assignment.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload File <span class="text-muted fw-normal small">(opsional)</span></label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Maksimal 10 MB</small>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Catatan / Jawaban</label>
                    <textarea name="notes" class="form-control" rows="5"
                              placeholder="Tuliskan jawaban atau catatan...">{{ old('notes', $submission?->notes) }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-send-check me-1"></i>{{ $submission ? 'Perbarui' : 'Kumpulkan' }} Tugas
                    </button>
                    <a href="{{ route('student.learn', $assignment->course) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="text-center mt-2">
        <a href="{{ route('student.learn', $assignment->course) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Kursus
        </a>
    </div>
    @endif

</div>
@endsection

@extends('layouts.admin')
@section('title', 'Detail Kursus')
@section('page-title', 'Detail Kursus')

@section('content')
<div class="row g-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="fw-bold mb-2">{{ $course->title }}</h4>
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    <span class="badge bg-primary">{{ $course->category?->name }}</span>
                    <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'pending' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($course->status) }}</span>
                    <span class="badge bg-info text-dark">{{ ucfirst($course->level) }}</span>
                </div>
                <p class="text-muted">{{ $course->description }}</p>
                @if($course->what_youll_learn)
                <h6 class="fw-bold mt-3">Yang Dipelajari:</h6>
                <p class="text-muted">{{ $course->what_youll_learn }}</p>
                @endif
                @if($course->requirements)
                <h6 class="fw-bold mt-3">Persyaratan:</h6>
                <p class="text-muted">{{ $course->requirements }}</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Kurikulum</h6></div>
            <div class="card-body p-0">
                @foreach($course->sections as $section)
                <div class="border-bottom px-3 py-2">
                    <strong>{{ $section->title }}</strong> <span class="text-muted small">({{ $section->lessons->count() }} materi)</span>
                    @foreach($section->lessons as $lesson)
                    <div class="ms-3 mt-1 small text-muted">
                        <i class="bi bi-{{ $lesson->type === 'video' ? 'play-circle' : 'file-text' }} me-1"></i>{{ $lesson->title }}
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Info Kursus</h6>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Tutor</dt><dd class="col-7">{{ $course->tutor->name }}</dd>
                    <dt class="col-5 text-muted">Harga</dt><dd class="col-7 text-primary fw-bold">{{ $course->formatted_price }}</dd>
                    <dt class="col-5 text-muted">Level</dt><dd class="col-7">{{ ucfirst($course->level) }}</dd>
                    <dt class="col-5 text-muted">Bahasa</dt><dd class="col-7">{{ $course->language }}</dd>
                    <dt class="col-5 text-muted">Dibuat</dt><dd class="col-7">{{ $course->created_at->format('d M Y') }}</dd>
                </dl>
            </div>
        </div>

        @if($course->status === 'pending')
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Tindakan</h6>
                <form action="{{ route('admin.courses.approve', $course) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-1"></i>Approve & Publish
                    </button>
                </form>
                <button class="btn btn-danger w-100" data-bs-toggle="collapse" data-bs-target="#rejectForm">
                    <i class="bi bi-x-circle me-1"></i>Tolak Kursus
                </button>
                <div class="collapse mt-3" id="rejectForm">
                    <form action="{{ route('admin.courses.reject', $course) }}" method="POST">
                        @csrf
                        <textarea name="rejection_reason" class="form-control mb-2" rows="3" required placeholder="Alasan penolakan..."></textarea>
                        <button type="submit" class="btn btn-danger btn-sm w-100">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @if($course->rejection_reason)
        <div class="alert alert-danger mt-3">
            <strong>Ditolak:</strong> {{ $course->rejection_reason }}
        </div>
        @endif
    </div>
</div>
@endsection

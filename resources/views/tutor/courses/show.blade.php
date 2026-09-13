@extends('layouts.tutor')
@section('title', $course->title)
@section('page-title', 'Kelola Kursus')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">{{ $course->title }}</h5>
        <div class="d-flex gap-2">
            <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'pending' ? 'warning text-dark' : ($course->status === 'rejected' ? 'danger' : 'secondary')) }}">{{ ucfirst($course->status) }}</span>
            <span class="text-muted small">{{ $course->enrollments->count() }} pelajar terdaftar</span>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('tutor.courses.edit', $course) }}" class="btn btn-outline-secondary btn-sm">Edit Kursus</a>
        <a href="{{ route('tutor.quizzes.index', $course) }}" class="btn btn-outline-info btn-sm">Quiz</a>
        <a href="{{ route('tutor.assignments.index', $course) }}" class="btn btn-outline-warning btn-sm">Tugas</a>
        <a href="{{ route('tutor.announcements.index', $course) }}" class="btn btn-outline-primary btn-sm">Pengumuman</a>
        <a href="{{ route('tutor.students.index', $course) }}" class="btn btn-outline-dark btn-sm">Pelajar</a>
        @if($course->status === 'draft')
        <form action="{{ route('tutor.courses.submit', $course) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">Submit untuk Review</button>
        </form>
        @endif
    </div>
</div>

@if($course->rejection_reason)
<div class="alert alert-danger">
    <strong><i class="bi bi-x-circle me-2"></i>Kursus Ditolak:</strong> {{ $course->rejection_reason }}
</div>
@endif

<div class="row g-4">
    <div class="col-md-8">
        <!-- Sections & Lessons -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Kurikulum Kursus</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                        <i class="bi bi-plus me-1"></i>Tambah Bagian
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse($course->sections as $section)
                <div class="border-bottom">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 bg-light">
                        <strong>{{ $section->title }}</strong>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tutor.lessons.create', $section) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus"></i> Tambah Materi
                            </a>
                            <form action="{{ route('tutor.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Hapus bagian ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    @foreach($section->lessons as $lesson)
                    <div class="d-flex align-items-center gap-3 px-4 py-2 border-top">
                        <i class="bi {{ $lesson->type === 'video' ? 'bi-play-circle text-primary' : ($lesson->type === 'document' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-text text-secondary') }}"></i>
                        <span class="flex-grow-1 small">{{ $lesson->title }}</span>
                        @if($lesson->is_free_preview)
                            <span class="badge bg-success bg-opacity-10 text-success" style="font-size:10px;">Preview</span>
                        @endif
                        <span class="text-muted" style="font-size:11px;">{{ $lesson->formatted_duration }}</span>
                        <a href="{{ route('tutor.lessons.edit', $lesson) }}" class="btn btn-sm btn-outline-secondary py-0 px-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('tutor.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Hapus materi?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    @endforeach
                    @if($section->lessons->isEmpty())
                    <div class="px-4 py-2 text-muted small text-center">Belum ada materi. <a href="{{ route('tutor.lessons.create', $section) }}">Tambah sekarang</a></div>
                    @endif
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <p>Belum ada bagian kursus.</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSectionModal">Tambah Bagian Pertama</button>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Info Kursus</h6>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Kategori</dt><dd class="col-7">{{ $course->category?->name ?? '-' }}</dd>
                    <dt class="col-5 text-muted">Level</dt><dd class="col-7">{{ ucfirst($course->level) }}</dd>
                    <dt class="col-5 text-muted">Harga</dt><dd class="col-7 text-primary fw-bold">{{ $course->formatted_price }}</dd>
                    <dt class="col-5 text-muted">Bahasa</dt><dd class="col-7">{{ $course->language }}</dd>
                    <dt class="col-5 text-muted">Pelajar</dt><dd class="col-7">{{ $course->enrollments->count() }}</dd>
                    <dt class="col-5 text-muted">Bagian</dt><dd class="col-7">{{ $course->sections->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tutor.sections.store', $course) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Tambah Bagian Kursus</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Nama Bagian</label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Bagian 1: Pengenalan" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

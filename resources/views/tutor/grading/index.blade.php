@extends('layouts.tutor')
@section('title', 'Penilaian Tugas')
@section('page-title', 'Penilaian Tugas')

@section('content')
<div class="mb-4">
    <a href="{{ route('tutor.assignments.index', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <h5 class="fw-bold mt-2">{{ $assignment->title }}</h5>
    <p class="text-muted small">Nilai Maksimum: {{ $assignment->max_score }} | Deadline: {{ $assignment->due_date?->format('d M Y H:i') ?? 'Tidak ada' }}</p>
</div>

<div class="row g-4">
    @forelse($submissions as $sub)
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">{{ $sub->user->name }}</h6>
                        <small class="text-muted">Dikumpulkan: {{ $sub->submitted_at?->format('d M Y H:i') }}</small>
                    </div>
                    @if($sub->score !== null)
                        <span class="badge bg-success">Dinilai: {{ $sub->score }}/{{ $assignment->max_score }}</span>
                    @else
                        <span class="badge bg-warning text-dark">Belum Dinilai</span>
                    @endif
                </div>

                @if($sub->notes)
                <div class="bg-light rounded p-2 mb-3 small">
                    <strong>Catatan:</strong> {{ $sub->notes }}
                </div>
                @endif

                @if($sub->file_path)
                <a href="{{ asset('storage/' . $sub->file_path) }}" class="btn btn-outline-primary btn-sm mb-3" target="_blank">
                    <i class="bi bi-download me-1"></i>Download File
                </a>
                @endif

                <form action="{{ route('tutor.grading.grade', $sub) }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col-5">
                            <label class="form-label small">Nilai (0-{{ $assignment->max_score }})</label>
                            <input type="number" name="score" class="form-control form-control-sm" value="{{ $sub->score }}" min="0" max="{{ $assignment->max_score }}" required>
                        </div>
                        <div class="col-7">
                            <label class="form-label small">Feedback</label>
                            <input type="text" name="feedback" class="form-control form-control-sm" value="{{ $sub->feedback }}" placeholder="Komentar singkat...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan Nilai</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">Belum ada submission.</div>
    @endforelse
</div>
<div class="mt-4">{{ $submissions->links() }}</div>
@endsection

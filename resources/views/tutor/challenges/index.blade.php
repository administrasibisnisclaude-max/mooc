@extends('layouts.tutor')
@section('title', 'Kelola Challenges')
@section('page-title', 'Kelola Mini Games / Challenges')

@push('styles')
<style>
.challenge-card { transition: box-shadow .2s; border: 1px solid #e9ecef; }
.challenge-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.08); }
.stat-pill { font-size: 12px; color: #6c757d; display:inline-flex; align-items:center; gap:4px; }
.badge-difficulty-easy   { background:#d1fae5; color:#065f46; }
.badge-difficulty-medium { background:#fef3c7; color:#92400e; }
.badge-difficulty-hard   { background:#fee2e2; color:#991b1b; }
.type-badge { background:#ede9fe; color:#5b21b6; font-size:11px; padding:2px 8px; border-radius:20px; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('tutor.courses.show', $course) }}" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>{{ Str::limit($course->title, 45) }}
        </a>
        <h4 class="fw-bold mb-0 mt-1">Mini Games <span class="text-muted fw-normal fs-6">— {{ $course->title }}</span></h4>
    </div>
    <a href="{{ route('tutor.challenges.create', $course) }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Buat Challenge
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats bar --}}
@if($challenges->count())
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-primary">{{ $challenges->count() }}</div>
            <div class="text-muted small">Total Challenge</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-success">{{ $challenges->where('is_active', true)->count() }}</div>
            <div class="text-muted small">Aktif</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-info">{{ $challenges->sum('levels_count') }}</div>
            <div class="text-muted small">Total Level</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-warning">{{ $challenges->sum('attempts_count') }}</div>
            <div class="text-muted small">Total Percobaan</div>
        </div>
    </div>
</div>
@endif

{{-- Challenge List --}}
<div class="row g-3">
    @forelse($challenges as $ch)
    <div class="col-12">
        <div class="card challenge-card">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">

                    {{-- Icon + Info --}}
                    <div class="col-lg-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-light" style="width:56px;height:56px;font-size:1.8rem;flex-shrink:0;">
                                {{ $ch->icon }}
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <h6 class="fw-bold mb-0">{{ $ch->title }}</h6>
                                    <span class="badge badge-difficulty-{{ $ch->difficulty }}">{{ ucfirst($ch->difficulty) }}</span>
                                    <span class="{{ $ch->is_active ? 'badge bg-success' : 'badge bg-secondary' }}">{{ $ch->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </div>
                                <span class="type-badge me-2">{{ $ch->type === 'problem_solving' ? '💡 Problem Solving' : '🧩 Puzzle Logic' }}</span>
                                <p class="text-muted small mb-0 mt-1">{{ Str::limit($ch->description, 70) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="col-lg-4">
                        <div class="d-flex flex-wrap gap-3">
                            <div class="text-center">
                                <div class="fw-bold fs-5 text-primary">{{ $ch->levels_count }}</div>
                                <div class="stat-pill"><i class="bi bi-layers"></i>Level</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold fs-5 text-success">{{ $ch->total_points }}</div>
                                <div class="stat-pill"><i class="bi bi-star"></i>Poin</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold fs-5 text-warning">{{ $ch->time_limit }}</div>
                                <div class="stat-pill"><i class="bi bi-clock"></i>Menit</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold fs-5 text-info">{{ $ch->attempts_count }}</div>
                                <div class="stat-pill"><i class="bi bi-people"></i>Percobaan</div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="col-lg-3 d-flex gap-2 justify-content-lg-end flex-wrap">
                        {{-- Toggle Aktif --}}
                        <form action="{{ route('tutor.challenges.update', [$course, $ch]) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="title" value="{{ $ch->title }}">
                            <input type="hidden" name="description" value="{{ $ch->description }}">
                            <input type="hidden" name="difficulty" value="{{ $ch->difficulty }}">
                            <input type="hidden" name="time_limit" value="{{ $ch->time_limit }}">
                            <input type="hidden" name="points_per_level" value="{{ $ch->points_per_level }}">
                            <input type="hidden" name="icon" value="{{ $ch->icon }}">
                            <input type="hidden" name="is_active" value="{{ $ch->is_active ? '0' : '1' }}">
                            <button type="submit" class="btn btn-sm {{ $ch->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                    title="{{ $ch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="bi {{ $ch->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-1"></i>
                                {{ $ch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <a href="{{ route('tutor.challenges.edit', [$course, $ch]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>Kelola
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete('{{ route('tutor.challenges.destroy', [$course, $ch]) }}', '{{ addslashes($ch->title) }}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-controller text-muted" style="font-size:3rem;"></i>
                <h5 class="mt-3 mb-1">Belum ada Challenge</h5>
                <p class="text-muted mb-3">Buat challenge pertama untuk mata kuliah ini agar mahasiswa bisa bermain sambil belajar.</p>
                <a href="{{ route('tutor.challenges.create', $course) }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Buat Challenge Pertama</a>
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Delete Modal --}}
<form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script>
function confirmDelete(url, title) {
    if (confirm(`Hapus challenge "${title}"?\n\nSemua level dan data percobaan siswa akan ikut terhapus.`)) {
        const form = document.getElementById('deleteForm');
        form.action = url;
        form.submit();
    }
}
</script>
@endpush

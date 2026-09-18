@extends('layouts.tutor')
@section('title', 'Mini Games & Challenges')
@section('page-title', 'Mini Games & Challenges')

@push('styles')
<style>
.course-card { border:1px solid #e9ecef; border-radius:10px; transition:box-shadow .2s; cursor:pointer; }
.course-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
.challenge-row { border-left:3px solid #dee2e6; transition:border-color .2s; }
.challenge-row:hover { border-left-color:#0d6efd; background:#f8f9ff; }
.badge-easy   { background:#d1fae5; color:#065f46; }
.badge-medium { background:#fef3c7; color:#92400e; }
.badge-hard   { background:#fee2e2; color:#991b1b; }
.stat-num { font-size:1.8rem; font-weight:700; line-height:1; }
.empty-course { border:2px dashed #dee2e6; border-radius:10px; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-controller text-primary me-2"></i>Mini Games & Challenges</h4>
        <p class="text-muted small mb-0">Buat dan kelola soal-soal game interaktif untuk setiap mata kuliah</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Global Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 border-0 shadow-sm">
            <div class="stat-num text-primary">{{ $challenges->count() }}</div>
            <div class="text-muted small mt-1">Total Challenge</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 border-0 shadow-sm">
            <div class="stat-num text-success">{{ $challenges->where('is_active',true)->count() }}</div>
            <div class="text-muted small mt-1">Aktif</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 border-0 shadow-sm">
            <div class="stat-num text-info">{{ $challenges->sum('levels_count') }}</div>
            <div class="text-muted small mt-1">Total Level</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 border-0 shadow-sm">
            <div class="stat-num text-warning">{{ $challenges->sum('attempts_count') }}</div>
            <div class="text-muted small mt-1">Total Percobaan</div>
        </div>
    </div>
</div>

{{-- Per-course sections --}}
@forelse($courses as $course)
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
        <div class="d-flex align-items-center gap-3">
            <div>
                <h6 class="fw-bold mb-0">{{ $course->title }}</h6>
                <span class="text-muted small">
                    {{ $course->challenges_count }} challenge
                    @if($course->active_challenges_count)
                    · <span class="text-success">{{ $course->active_challenges_count }} aktif</span>
                    @endif
                </span>
            </div>
        </div>
        <a href="{{ route('tutor.challenges.create', $course) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Buat Challenge
        </a>
    </div>

    @php $courseChallenges = $challenges->where('course_id', $course->id); @endphp

    @if($courseChallenges->isEmpty())
    <div class="card-body">
        <div class="empty-course text-center py-4 text-muted">
            <i class="bi bi-controller" style="font-size:2rem;opacity:.4;"></i>
            <p class="mt-2 mb-2 small">Belum ada challenge untuk mata kuliah ini.</p>
            <a href="{{ route('tutor.challenges.create', $course) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus me-1"></i>Buat Challenge Pertama
            </a>
        </div>
    </div>
    @else
    <div class="list-group list-group-flush">
        @foreach($courseChallenges as $ch)
        <div class="list-group-item challenge-row px-4 py-3">
            <div class="row align-items-center g-2">

                {{-- Icon + Info --}}
                <div class="col-lg-5 d-flex align-items-center gap-3">
                    <span style="font-size:1.6rem;flex-shrink:0;">{{ $ch->icon }}</span>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-semibold">{{ $ch->title }}</span>
                            <span class="badge badge-{{ $ch->difficulty }} rounded-pill" style="font-size:10px;">{{ ucfirst($ch->difficulty) }}</span>
                            <span class="badge {{ $ch->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill" style="font-size:10px;">
                                {{ $ch->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-layers me-1"></i>{{ $ch->levels_count }} level
                            <i class="bi bi-star ms-2 me-1"></i>{{ $ch->levels_count * $ch->points_per_level }} poin
                            <i class="bi bi-clock ms-2 me-1"></i>{{ $ch->time_limit }} mnt
                            <i class="bi bi-people ms-2 me-1"></i>{{ $ch->attempts_count }}x dicoba
                        </div>
                    </div>
                </div>

                {{-- Type --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <span class="badge rounded-pill" style="background:#ede9fe;color:#5b21b6;font-size:11px;padding:4px 10px;">
                        {{ $ch->type === 'problem_solving' ? '💡 Problem Solving' : '🧩 Puzzle Logic' }}
                    </span>
                </div>

                {{-- Actions --}}
                <div class="col-lg-4 d-flex gap-2 justify-content-lg-end flex-wrap">
                    {{-- Toggle aktif --}}
                    <form action="{{ route('tutor.challenges.update', [$course, $ch]) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="title" value="{{ $ch->title }}">
                        <input type="hidden" name="description" value="{{ $ch->description }}">
                        <input type="hidden" name="difficulty" value="{{ $ch->difficulty }}">
                        <input type="hidden" name="time_limit" value="{{ $ch->time_limit }}">
                        <input type="hidden" name="points_per_level" value="{{ $ch->points_per_level }}">
                        <input type="hidden" name="icon" value="{{ $ch->icon }}">
                        <input type="hidden" name="is_active" value="{{ $ch->is_active ? '0' : '1' }}">
                        <button type="submit" class="btn btn-sm {{ $ch->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                            <i class="bi {{ $ch->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                            {{ $ch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <a href="{{ route('tutor.challenges.edit', [$course, $ch]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil-square me-1"></i>Kelola
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="confirmDelete('{{ route('tutor.challenges.destroy', [$course, $ch]) }}','{{ addslashes($ch->title) }}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="card-footer text-end bg-transparent py-2">
        <a href="{{ route('tutor.challenges.index', $course) }}" class="text-primary text-decoration-none small">
            Lihat semua challenge mata kuliah ini <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    @endif
</div>
@empty
<div class="card text-center py-5">
    <div class="card-body">
        <i class="bi bi-book text-muted" style="font-size:3rem;"></i>
        <h5 class="mt-3 mb-1">Belum ada mata kuliah</h5>
        <p class="text-muted mb-3">Buat mata kuliah terlebih dahulu sebelum menambahkan challenge.</p>
        <a href="{{ route('tutor.courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus me-1"></i>Buat Mata Kuliah
        </a>
    </div>
</div>
@endforelse

<form id="deleteForm" method="POST" style="display:none">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script>
function confirmDelete(url, title) {
    if (confirm(`Hapus challenge "${title}"?\n\nSemua level dan riwayat percobaan siswa akan terhapus permanen.`)) {
        const f = document.getElementById('deleteForm');
        f.action = url;
        f.submit();
    }
}
</script>
@endpush

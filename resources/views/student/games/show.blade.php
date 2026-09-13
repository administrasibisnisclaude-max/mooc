@extends('layouts.app')

@section('title', $challenge->title . ' - Mini Games')

@push('styles')
<style>
    .game-hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .level-dot { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; border: 3px solid rgba(255,255,255,0.4); }
    .level-dot.locked { background: rgba(255,255,255,0.2); color: rgba(255,255,255,0.6); }
    .stat-card { background: rgba(255,255,255,0.15); border-radius: 12px; padding: 20px; text-align: center; }
</style>
@endpush

@section('content')
<div class="game-hero text-white py-5">
    <div class="container">
        <a href="{{ route('student.games.index') }}" class="btn btn-outline-light btn-sm mb-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span style="font-size:3.5rem;">{{ $challenge->icon }}</span>
                    <div>
                        <h1 class="fw-bold mb-1">{{ $challenge->title }}</h1>
                        <span class="badge bg-white text-dark rounded-pill px-3">{{ ucfirst($challenge->difficulty) }}</span>
                    </div>
                </div>
                <p class="lead opacity-90 mb-4">{{ $challenge->description }}</p>
                <div class="row g-3 mb-4">
                    <div class="col-auto"><div class="stat-card"><div class="fs-4 fw-bold">{{ $challenge->levels->count() }}</div><small class="opacity-75">Total Level</small></div></div>
                    <div class="col-auto"><div class="stat-card"><div class="fs-4 fw-bold">{{ $challenge->points_per_level }} pt</div><small class="opacity-75">Per Level</small></div></div>
                    <div class="col-auto"><div class="stat-card"><div class="fs-4 fw-bold">{{ $challenge->time_limit }} mnt</div><small class="opacity-75">Batas Waktu</small></div></div>
                    <div class="col-auto"><div class="stat-card"><div class="fs-4 fw-bold">{{ $challenge->levels->count() * $challenge->points_per_level }}</div><small class="opacity-75">Maks Poin</small></div></div>
                </div>
                @if($bestAttempt)
                <div class="alert alert-warning text-dark rounded-3">
                    <i class="bi bi-star-fill me-2"></i>
                    Skor terbaik kamu: <strong>{{ $bestAttempt->score }} poin</strong>
                    ({{ $bestAttempt->levels_completed }}/{{ $challenge->levels->count() }} level selesai)
                </div>
                @endif
                <a href="{{ route('student.games.play', $challenge) }}" class="btn btn-warning btn-lg fw-bold px-5 shadow">
                    <i class="bi bi-play-fill me-2"></i>
                    {{ $bestAttempt ? 'Main Lagi' : 'Mulai Bermain' }}
                </a>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-4">
            @foreach($challenge->levels as $level)
            <div class="level-dot locked" title="Level {{ $level->level_number }}: {{ $level->title }}">{{ $level->level_number }}</div>
            @endforeach
        </div>
        <small class="opacity-75 d-block mt-2">{{ $challenge->levels->count() }} level menanti!</small>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4">Cara Bermain</h3>
            <div class="row g-3">
                <div class="col-md-6"><div class="card border-0 bg-light rounded-4 h-100"><div class="card-body p-4"><div class="fs-2 mb-2">⏱️</div><h6 class="fw-bold">Perhatikan Waktu</h6><p class="text-muted small mb-0">Total waktu {{ $challenge->time_limit }} menit untuk menyelesaikan semua level.</p></div></div></div>
                <div class="col-md-6"><div class="card border-0 bg-light rounded-4 h-100"><div class="card-body p-4"><div class="fs-2 mb-2">💡</div><h6 class="fw-bold">Gunakan Hint</h6><p class="text-muted small mb-0">Setiap level punya petunjuk. Klik tombol Hint untuk bantuan.</p></div></div></div>
                <div class="col-md-6"><div class="card border-0 bg-light rounded-4 h-100"><div class="card-body p-4"><div class="fs-2 mb-2">🎯</div><h6 class="fw-bold">Jawaban Benar = Poin</h6><p class="text-muted small mb-0">Setiap jawaban benar menghasilkan {{ $challenge->points_per_level }} poin.</p></div></div></div>
                <div class="col-md-6"><div class="card border-0 bg-light rounded-4 h-100"><div class="card-body p-4"><div class="fs-2 mb-2">🏆</div><h6 class="fw-bold">Naik Leaderboard</h6><p class="text-muted small mb-0">Skor terbaikmu bersaing dengan pemain lain di leaderboard global!</p></div></div></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-list-ol me-2"></i>Level dalam Tantangan</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($challenge->levels as $level)
                        <li class="list-group-item d-flex align-items-center gap-2 py-3">
                            <span class="badge bg-primary rounded-pill" style="min-width:28px;">{{ $level->level_number }}</span>
                            <span class="flex-grow-1 small">{{ $level->title }}</span>
                            <span class="badge bg-light text-dark border small">{{ $challenge->points_per_level }}pt</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

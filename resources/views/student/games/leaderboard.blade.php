@extends('layouts.app')

@section('title', 'Leaderboard - Mini Games')

@push('styles')
<style>
    .lb-hero { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    .rank-badge { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.1rem; flex-shrink: 0; }
    .rank-1 { background: linear-gradient(135deg, #ffd700, #ffb800); color: #000; }
    .rank-2 { background: linear-gradient(135deg, #c0c0c0, #909090); color: #000; }
    .rank-3 { background: linear-gradient(135deg, #cd7f32, #a05c20); color: #fff; }
    .rank-other { background: #e9ecef; color: #495057; }
</style>
@endpush

@section('content')
<div class="lb-hero py-5">
    <div class="container">
        <a href="{{ route('student.games.index') }}" class="btn btn-outline-dark btn-sm mb-4">
            <i class="bi bi-arrow-left me-1"></i> Mini Games
        </a>
        <h1 class="fw-bold display-5 mb-2">🏆 Leaderboard</h1>
        <p class="lead mb-0">Siapa yang paling jago? Lihat ranking global pemain terbaik!</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold">Top 20 Pemain</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($leaderboard as $i => $row)
                    @php $rank = $i + 1; @endphp
                    <div class="d-flex align-items-center gap-3 px-4 py-3 {{ $i < 19 ? 'border-bottom' : '' }} {{ auth()->id() == $row->user_id ? 'bg-primary bg-opacity-10' : '' }}">
                        <div class="rank-badge rank-{{ $rank <= 3 ? $rank : 'other' }}">
                            @if($rank <= 3)
                                {{ ['🥇','🥈','🥉'][$rank-1] }}
                            @else
                                {{ $rank }}
                            @endif
                        </div>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:14px;flex-shrink:0;">
                            {{ strtoupper(substr($row->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                {{ $row->user->name ?? 'Unknown' }}
                                @if(auth()->id() == $row->user_id)
                                    <span class="badge bg-primary ms-1">Kamu</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $row->challenges_played }} tantangan diselesaikan</small>
                        </div>
                        @if($row->best_time)
                        <div class="text-center d-none d-md-block">
                            <div class="small text-muted">Best Time</div>
                            <div class="fw-semibold">{{ gmdate('i:s', $row->best_time) }}</div>
                        </div>
                        @endif
                        <div class="text-end">
                            <div class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                                {{ number_format($row->total_score) }} pt
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <div class="display-4 mb-3">🎮</div>
                        <p>Belum ada data. Jadilah yang pertama!</p>
                        <a href="{{ route('student.games.index') }}" class="btn btn-primary">Mulai Bermain</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-primary text-white rounded-top-4 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-controller me-2"></i>Tantangan Tersedia</h6>
                </div>
                <div class="card-body">
                    @foreach($challenges as $ch)
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span style="font-size:2rem;">{{ $ch->icon }}</span>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">{{ $ch->title }}</div>
                            <div class="text-muted" style="font-size:12px;">{{ $ch->levels_count }} level &middot; {{ $ch->levels_count * $ch->points_per_level }} poin maks</div>
                        </div>
                        <a href="{{ route('student.games.show', $ch) }}" class="btn btn-sm btn-outline-primary">Main</a>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 bg-warning">
                <div class="card-body text-center p-4">
                    <div class="display-4 mb-2">🎯</div>
                    <h6 class="fw-bold">Ingin naik ranking?</h6>
                    <p class="small mb-3">Selesaikan lebih banyak tantangan dan raih skor sempurna!</p>
                    <a href="{{ route('student.games.index') }}" class="btn btn-dark btn-sm">Mulai Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

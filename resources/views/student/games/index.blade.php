@extends('layouts.app')

@section('title', 'Mini Games - EduPlatform')

@push('styles')
<style>
    .game-hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .challenge-card { border: none; border-radius: 16px; transition: all 0.3s; overflow: hidden; cursor: pointer; }
    .challenge-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.15) !important; }
    .challenge-icon { font-size: 4rem; line-height: 1; }
    .difficulty-badge.easy { background: #d1fae5; color: #065f46; }
    .difficulty-badge.medium { background: #fef3c7; color: #92400e; }
    .difficulty-badge.hard { background: #fee2e2; color: #991b1b; }
    .stat-box { background: rgba(255,255,255,0.15); border-radius: 12px; padding: 16px; }
</style>
@endpush

@section('content')
<div class="game-hero text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold mb-2">🎮 Mini Games</h1>
                <p class="lead mb-4 opacity-90">Asah kemampuan logika, coding, dan problem-solving sambil bermain! Kumpulkan poin dan raih posisi tertinggi di leaderboard.</p>
                <a href="{{ route('student.games.leaderboard') }}" class="btn btn-light btn-lg fw-semibold">
                    <i class="bi bi-trophy-fill text-warning me-2"></i>Lihat Leaderboard
                </a>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <div class="display-1">🏆</div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Pilih Tantangan</h2>
    <div class="row g-4 mb-5">
        @foreach($challenges as $challenge)
        @php
            $best = $challenge->attempts->first();
            $totalLevels = $challenge->levels->count();
            $maxScore = $totalLevels * $challenge->points_per_level;
            $pct = $best ? min(100, round($best->score / max($maxScore,1) * 100)) : 0;
        @endphp
        <div class="col-md-6">
            <a href="{{ route('student.games.show', $challenge) }}" class="text-decoration-none">
                <div class="challenge-card card shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="challenge-icon">{{ $challenge->icon }}</div>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-1 text-dark">{{ $challenge->title }}</h4>
                                <span class="badge difficulty-badge {{ $challenge->difficulty }} rounded-pill px-3 py-1 text-capitalize">
                                    {{ $challenge->difficulty }}
                                </span>
                            </div>
                        </div>
                        <p class="text-muted mb-3">{{ $challenge->description }}</p>
                        <div class="row g-2 text-center mb-3">
                            <div class="col-4">
                                <div class="bg-light rounded-3 p-2">
                                    <div class="fw-bold text-primary">{{ $totalLevels }}</div>
                                    <small class="text-muted">Level</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded-3 p-2">
                                    <div class="fw-bold text-success">{{ $challenge->points_per_level }}pt</div>
                                    <small class="text-muted">Per Level</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded-3 p-2">
                                    <div class="fw-bold text-warning">{{ $challenge->time_limit }}m</div>
                                    <small class="text-muted">Waktu</small>
                                </div>
                            </div>
                        </div>
                        @if($best)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Skor terbaik: <strong>{{ $best->score }}/{{ $maxScore }}</strong></small>
                                <small class="text-muted">{{ $pct }}%</small>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success">
                            <i class="bi bi-check-circle me-1"></i>Sudah dimainkan
                        </span>
                        @else
                        <span class="badge bg-primary-subtle text-primary">
                            <i class="bi bi-play-circle me-1"></i>Belum dimainkan
                        </span>
                        @endif
                    </div>
                    <div class="card-footer bg-primary text-white text-center py-3">
                        <strong>Mulai Tantangan <i class="bi bi-arrow-right ms-1"></i></strong>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    @if($leaderboard->count())
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-warning text-dark py-3 rounded-top-4">
            <h5 class="mb-0 fw-bold"><i class="bi bi-trophy-fill me-2"></i>Top 10 Pemain</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Pemain</th>
                            <th class="text-center">Tantangan</th>
                            <th class="text-end pe-4">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaderboard as $i => $row)
                        <tr>
                            <td class="ps-4">
                                @if($i === 0) <span class="fs-5">🥇</span>
                                @elseif($i === 1) <span class="fs-5">🥈</span>
                                @elseif($i === 2) <span class="fs-5">🥉</span>
                                @else <span class="text-muted fw-bold">{{ $i+1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:12px;flex-shrink:0;">
                                        {{ strtoupper(substr($row->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $row->user->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="text-center">{{ $row->challenges_played }}</td>
                            <td class="text-end pe-4"><span class="badge bg-warning text-dark fs-6 px-3">{{ number_format($row->total_score) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-center py-3">
            <a href="{{ route('student.games.leaderboard') }}" class="btn btn-outline-warning btn-sm">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

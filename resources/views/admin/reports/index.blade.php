@extends('layouts.admin')
@section('title', 'Laporan')
@section('page-title', 'Laporan Platform')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <p class="text-muted small mb-1">Total Pendapatan</p>
            <h3 class="fw-bold text-primary">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <p class="text-muted small mb-1">Kursus Selesai</p>
            <h3 class="fw-bold text-success">{{ number_format($stats['total_completions']) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <p class="text-muted small mb-1">Rata-rata Progress</p>
            <h3 class="fw-bold text-warning">{{ round($stats['avg_progress'], 1) }}%</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Pendaftaran per Bulan ({{ date('Y') }})</h6></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th class="px-3">Bulan</th><th>Pendaftaran</th></tr></thead>
                    <tbody>
                        @php $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des']; @endphp
                        @forelse($monthlyEnrollments as $me)
                        <tr>
                            <td class="px-3">{{ $months[$me->month] }} {{ $me->year }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px;">
                                        <div class="progress-bar" style="width:{{ min(100, $me->count * 10) }}%"></div>
                                    </div>
                                    <span class="fw-semibold">{{ $me->count }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center py-3 text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Top 10 Kursus Terpopuler</h6></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th class="px-3">Kursus</th><th>Pelajar</th></tr></thead>
                    <tbody>
                        @forelse($topCourses as $i => $course)
                        <tr>
                            <td class="px-3">
                                <span class="badge bg-primary me-2">{{ $i + 1 }}</span>
                                {{ Str::limit($course->title, 40) }}
                            </td>
                            <td class="fw-semibold">{{ $course->enrollments_count }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center py-3 text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.tutor')
@section('title', 'Monitoring Pelajar')
@section('page-title', 'Monitoring Pelajar')

@section('content')
<div class="mb-4">
    <a href="{{ route('tutor.courses.show', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <h5 class="fw-bold mt-2">{{ $course->title }}</h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th class="px-3">Pelajar</th><th>Progress</th><th>Terdaftar</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                <tr>
                    <td class="px-3">
                        <strong>{{ $enrollment->user->name }}</strong>
                        <p class="mb-0 text-muted small">{{ $enrollment->user->email }}</p>
                    </td>
                    <td class="align-middle" style="width:200px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px;">
                                <div class="progress-bar" style="width:{{ $enrollment->progress_percentage }}%"></div>
                            </div>
                            <small>{{ round($enrollment->progress_percentage) }}%</small>
                        </div>
                    </td>
                    <td class="align-middle text-muted small">{{ $enrollment->enrolled_at?->format('d M Y') ?? $enrollment->created_at->format('d M Y') }}</td>
                    <td class="align-middle">
                        @if($enrollment->completed_at)
                            <span class="badge bg-success bg-opacity-10 text-success">Selesai</span>
                        @else
                            <span class="badge bg-primary bg-opacity-10 text-primary">Berjalan</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <a href="{{ route('tutor.students.show', [$course, $enrollment->user_id]) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada pelajar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $enrollments->links() }}</div>
</div>
@endsection

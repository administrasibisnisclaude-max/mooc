@extends('layouts.tutor')
@section('title', 'Challenges')
@section('page-title', 'Kelola Challenges')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('tutor.courses.show', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali ke {{ Str::limit($course->title, 40) }}</a>
    </div>
    <a href="{{ route('tutor.challenges.create', $course) }}" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Buat Challenge</a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    @forelse($challenges as $challenge)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:1.5rem;">{{ $challenge->icon }}</span>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $challenge->title }}</h6>
                            <span class="badge bg-{{ $challenge->difficulty_badge }} badge-sm">{{ ucfirst($challenge->difficulty) }}</span>
                        </div>
                    </div>
                    <span class="badge {{ $challenge->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $challenge->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">{{ Str::limit($challenge->description, 80) }}</p>
                <div class="d-flex gap-3 text-muted small mb-3">
                    <span><i class="bi bi-layers me-1"></i>{{ $challenge->levels_count }} level</span>
                    <span><i class="bi bi-star me-1"></i>{{ $challenge->total_points }} poin</span>
                    <span><i class="bi bi-clock me-1"></i>{{ $challenge->time_limit }} mnt</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tutor.challenges.edit', [$course, $challenge]) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                    <form action="{{ route('tutor.challenges.destroy', [$course, $challenge]) }}" method="POST" onsubmit="return confirm('Hapus challenge ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-controller display-4 mb-3 d-block"></i>
        <p>Belum ada challenge untuk mata kuliah ini.</p>
        <a href="{{ route('tutor.challenges.create', $course) }}" class="btn btn-primary">Buat Challenge</a>
    </div>
    @endforelse
</div>
@endsection

@extends('layouts.tutor')
@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman')

@section('content')
<div class="mb-4">
    <a href="{{ route('tutor.courses.show', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali ke {{ Str::limit($course->title, 40) }}</a>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Buat Pengumuman</h6></div>
            <div class="card-body">
                <form action="{{ route('tutor.announcements.store', $course) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Judul</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Isi Pengumuman</label>
                        <textarea name="content" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Kirim Pengumuman</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        @forelse($announcements as $ann)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="fw-bold mb-1">{{ $ann->title }}</h6>
                    <form action="{{ route('tutor.announcements.destroy', [$course, $ann]) }}" method="POST" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
                <p class="text-muted small mb-1">{{ $ann->content }}</p>
                <small class="text-muted">{{ $ann->created_at->diffForHumans() }}</small>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">Belum ada pengumuman.</div>
        @endforelse
        {{ $announcements->links() }}
    </div>
</div>
@endsection

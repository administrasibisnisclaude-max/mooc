@extends('layouts.app')
@section('title', 'Forum Diskusi')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('student.learn', $course) }}" class="text-white-50">Kursus</a></li>
                <li class="breadcrumb-item active text-white">Forum</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0"><i class="bi bi-chat-dots me-2"></i>Forum Diskusi</h4>
        <p class="mb-0 opacity-75 small">{{ $course->title }}</p>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h6 class="fw-bold mb-0">Buat Topik Baru</h6></div>
                <div class="card-body">
                    <form action="{{ route('student.forum.store', $course) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Judul Topik</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Pertanyaan / Deskripsi</label>
                            <textarea name="content" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Buat Topik</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">{{ $threads->total() }} Topik Diskusi</h6>
            </div>

            @forelse($threads as $thread)
            <div class="card border-0 shadow-sm mb-3 {{ $thread->is_pinned ? 'border-start border-3 border-warning' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            @if($thread->is_pinned)<span class="badge bg-warning text-dark me-2 small"><i class="bi bi-pin-angle"></i> Disematkan</span>@endif
                            <a href="{{ route('student.forum.show', [$course, $thread]) }}" class="text-decoration-none text-dark">
                                <h6 class="fw-bold mb-1 d-inline">{{ $thread->title }}</h6>
                            </a>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $thread->replies->count() }} balasan</span>
                    </div>
                    <p class="text-muted small mb-2">{{ Str::limit($thread->content, 120) }}</p>
                    <div class="d-flex align-items-center gap-3 text-muted" style="font-size:12px;">
                        <span><img src="https://ui-avatars.com/api/?name={{ urlencode($thread->user->name) }}&size=20&background=0056D2&color=fff" class="rounded-circle me-1" style="width:20px;height:20px;"> {{ $thread->user->name }}</span>
                        <span>{{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-chat-dots fs-1"></i>
                <p class="mt-2">Belum ada diskusi. Jadilah yang pertama!</p>
            </div>
            @endforelse

            {{ $threads->links() }}
        </div>
    </div>
</div>
@endsection

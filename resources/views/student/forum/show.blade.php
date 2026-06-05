@extends('layouts.app')
@section('title', $thread->title)

@section('content')
<div class="container py-4" style="max-width:900px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('student.forum.index', $course) }}">Forum</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($thread->title, 40) }}</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h4 class="fw-bold mb-2">{{ $thread->title }}</h4>
            <div class="d-flex align-items-center gap-3 mb-3 text-muted small">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($thread->user->name) }}&size=30&background=0056D2&color=fff" class="rounded-circle" style="width:30px;height:30px;">
                <strong class="text-dark">{{ $thread->user->name }}</strong>
                <span>{{ $thread->created_at->format('d M Y H:i') }}</span>
            </div>
            <div class="prose">{!! nl2br(e($thread->content)) !!}</div>
        </div>
    </div>

    <h6 class="fw-bold mb-3">{{ $thread->replies->count() }} Balasan</h6>

    @foreach($thread->replies as $reply)
    <div class="card border-0 shadow-sm mb-3 {{ $reply->is_answer ? 'border-start border-3 border-success' : '' }}">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-2">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}&size=35&background=0056D2&color=fff" class="rounded-circle" style="width:35px;height:35px;">
                <div>
                    <strong class="small">{{ $reply->user->name }}</strong>
                    <p class="mb-0 text-muted" style="font-size:11px;">{{ $reply->created_at->diffForHumans() }}</p>
                </div>
                @if($reply->is_answer)
                <span class="badge bg-success ms-auto"><i class="bi bi-check-circle me-1"></i>Jawaban</span>
                @endif
            </div>
            <p class="mb-2">{!! nl2br(e($reply->content)) !!}</p>
            @if($thread->user_id === auth()->id())
            <form action="{{ route('student.forum.mark-answer', $reply) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-{{ $reply->is_answer ? 'secondary' : 'success' }}">
                    {{ $reply->is_answer ? 'Batalkan Jawaban' : 'Tandai Jawaban' }}
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white"><h6 class="fw-bold mb-0">Tulis Balasan</h6></div>
        <div class="card-body">
            <form action="{{ route('student.forum.reply', [$course, $thread]) }}" method="POST">
                @csrf
                <textarea name="content" class="form-control mb-3" rows="4" placeholder="Tulis balasan Anda..." required></textarea>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Kirim Balasan</button>
                <a href="{{ route('student.forum.index', $course) }}" class="btn btn-outline-secondary ms-2">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection

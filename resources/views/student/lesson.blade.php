@extends('layouts.app')
@section('title', $lesson->title)

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-lg-8">
            @if($lesson->type === 'video' && $lesson->video_url)
            <div class="ratio ratio-16x9 bg-black">
                @php
                    $url = $lesson->video_url;
                    if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
                        preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]+)/', $url, $m);
                        $embedUrl = 'https://www.youtube.com/embed/' . ($m[1] ?? '');
                    } else {
                        $embedUrl = $url;
                    }
                @endphp
                <iframe src="{{ $embedUrl }}" allowfullscreen></iframe>
            </div>
            @else
            <div class="bg-light p-4" style="min-height:350px;">
                <div class="d-flex align-items-center justify-content-center" style="height:100%;">
                    <div class="text-center">
                        <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
                        <p class="text-muted mt-2">{{ ucfirst($lesson->type) }} Materi</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="p-4">
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb small">
                                <li class="breadcrumb-item"><a href="{{ route('student.learn', $course) }}">{{ Str::limit($course->title, 30) }}</a></li>
                                <li class="breadcrumb-item active">{{ Str::limit($lesson->title, 40) }}</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-0">{{ $lesson->title }}</h4>
                    </div>
                    <div class="d-flex gap-2">
                        @if(!$progress->completed_at)
                        <form action="{{ route('student.lesson.complete', $lesson) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check-circle me-1"></i>Tandai Selesai
                            </button>
                        </form>
                        @else
                        <span class="badge bg-success py-2 px-3"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                        @endif
                    </div>
                </div>

                @if($lesson->content)
                <div class="prose mt-3">
                    {!! nl2br(e($lesson->content)) !!}
                </div>
                @endif

                @if($lesson->materials->count() > 0)
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Materi Pendukung</h6>
                    @foreach($lesson->materials as $material)
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded mb-2">
                        <i class="bi bi-file-earmark text-primary fs-4"></i>
                        <div class="flex-grow-1">
                            <strong class="small">{{ $material->title }}</strong>
                            <p class="mb-0 text-muted" style="font-size:11px;">{{ $material->file_type }} &bull; {{ $material->formatted_size }}</p>
                        </div>
                        <a href="{{ asset('storage/' . $material->file_path) }}" class="btn btn-sm btn-outline-primary" download>
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    @if($prevLesson)
                    <a href="{{ route('student.lesson', $prevLesson) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                    </a>
                    @else
                    <span></span>
                    @endif
                    @if($nextLesson)
                    <a href="{{ route('student.lesson', $nextLesson) }}" class="btn btn-primary">
                        Selanjutnya <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 border-start d-none d-lg-block" style="height:calc(100vh - 70px);overflow-y:auto;">
            <div class="p-3 bg-light border-bottom">
                <h6 class="fw-bold mb-0">Kurikulum</h6>
            </div>
            @foreach($course->sections as $section)
            <div class="border-bottom">
                <div class="px-3 py-2 bg-white fw-semibold small">{{ $section->title }}</div>
                @foreach($section->lessons as $l)
                <a href="{{ route('student.lesson', $l) }}" class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top {{ $l->id === $lesson->id ? 'bg-primary bg-opacity-10' : '' }}" style="color:inherit;">
                    <i class="bi bi-{{ $l->type === 'video' ? 'play-circle' : 'file-text' }} {{ $l->id === $lesson->id ? 'text-primary' : 'text-muted' }}" style="font-size:13px;"></i>
                    <span style="font-size:13px;" class="{{ $l->id === $lesson->id ? 'fw-semibold text-primary' : '' }}">{{ $l->title }}</span>
                </a>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

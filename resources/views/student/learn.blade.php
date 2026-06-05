@extends('layouts.app')
@section('title', $course->title)

@section('content')
<div class="container-fluid py-0">
    <div class="row g-0">
        <!-- Main Content -->
        <div class="col-lg-8 p-4">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.courses') }}">Kursus Saya</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($course->title, 40) }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">{{ $course->title }}</h4>
            <div class="d-flex gap-3 mb-4">
                <span class="text-muted small">Tutor: {{ $course->tutor->name }}</span>
                <span class="text-muted small">
                    <div class="progress d-inline-flex align-items-center gap-2" style="width:auto;height:auto;background:none;">
                        <span>Progress: {{ round($enrollment->progress_percentage) }}%</span>
                    </div>
                </span>
            </div>

            <div class="progress mb-4" style="height:8px;">
                <div class="progress-bar" style="width:{{ $enrollment->progress_percentage }}%"></div>
            </div>

            <!-- Announcements -->
            @if($course->announcements->count() > 0)
            <div class="alert alert-info">
                <h6 class="fw-bold"><i class="bi bi-megaphone me-2"></i>Pengumuman Terbaru</h6>
                @foreach($course->announcements->take(2) as $ann)
                <div class="mb-2">
                    <strong>{{ $ann->title }}</strong>
                    <p class="mb-0 small">{{ Str::limit($ann->content, 150) }}</p>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Navigation buttons -->
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('student.forum.index', $course) }}" class="btn btn-outline-primary">
                    <i class="bi bi-chat-dots me-1"></i>Forum Diskusi
                </a>
                @if($enrollment->progress_percentage >= 100)
                <a href="{{ route('student.certificates') }}" class="btn btn-success">
                    <i class="bi bi-award me-1"></i>Lihat Sertifikat
                </a>
                @endif
            </div>
        </div>

        <!-- Sidebar Curriculum -->
        <div class="col-lg-4 border-start" style="min-height:calc(100vh - 70px);">
            <div class="p-3 bg-light border-bottom">
                <h6 class="fw-bold mb-0">Kurikulum Kursus</h6>
            </div>
            <div style="overflow-y:auto;max-height:calc(100vh - 130px);">
                @foreach($course->sections as $section)
                <div class="border-bottom">
                    <div class="px-3 py-2 bg-white fw-semibold small">{{ $section->title }}</div>
                    @foreach($section->lessons as $lesson)
                    @php $isCompleted = in_array($lesson->id, $completedLessonIds); @endphp
                    <a href="{{ route('student.lesson', $lesson) }}" class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top {{ $isCompleted ? 'bg-success bg-opacity-5' : '' }}" style="color:inherit;">
                        <i class="bi {{ $isCompleted ? 'bi-check-circle-fill text-success' : ($lesson->type === 'video' ? 'bi-play-circle text-primary' : 'bi-file-text text-secondary') }}" style="font-size:14px;"></i>
                        <span class="flex-grow-1" style="font-size:13px;">{{ $lesson->title }}</span>
                        @if($lesson->duration)
                        <span class="text-muted" style="font-size:11px;">{{ $lesson->formatted_duration }}</span>
                        @endif
                    </a>
                    @endforeach

                    <!-- Quizzes in section -->
                    @foreach($section->quizzes as $quiz)
                    <a href="{{ route('student.quiz.start', $quiz) }}" class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top" style="color:inherit;">
                        <i class="bi bi-patch-question text-warning" style="font-size:14px;"></i>
                        <span style="font-size:13px;">Quiz: {{ $quiz->title }}</span>
                    </a>
                    @endforeach

                    <!-- Assignments in section -->
                    @foreach($section->assignments as $assignment)
                    <a href="{{ route('student.assignment', $assignment) }}" class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top" style="color:inherit;">
                        <i class="bi bi-clipboard2-check text-danger" style="font-size:14px;"></i>
                        <span style="font-size:13px;">Tugas: {{ $assignment->title }}</span>
                    </a>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

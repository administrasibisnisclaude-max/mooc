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

            {{-- ── Course completed: review prompt ──────────────── --}}
            @if($enrollment->progress_percentage >= 100)
            @php
                $myReview = $course->reviews->firstWhere('user_id', auth()->id());
            @endphp
            <div class="card border-0 mt-4 {{ $myReview ? 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10' }}">
                <div class="card-body py-3 px-4">
                    @if($myReview)
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div>
                            <p class="fw-semibold mb-1">
                                <i class="bi bi-star-fill text-warning me-1"></i>Ulasan Anda
                            </p>
                            <div class="d-flex gap-1 mb-1">
                                @for($i=1;$i<=5;$i++)
                                <i class="bi {{ $i<=$myReview->rating ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}" style="font-size:14px;"></i>
                                @endfor
                                <span class="text-muted small ms-1">{{ $myReview->rating }}/5</span>
                            </div>
                            @if($myReview->review)
                            <p class="mb-0 small text-muted">{{ Str::limit($myReview->review, 100) }}</p>
                            @endif
                        </div>
                        <a href="{{ route('student.review.create', $course) }}" class="btn btn-warning btn-sm ms-auto">
                            <i class="bi bi-pencil me-1"></i>Edit Ulasan
                        </a>
                    </div>
                    @else
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div>
                            <p class="fw-bold mb-1 text-success">
                                <i class="bi bi-trophy-fill me-1"></i>Selamat! Kursus Selesai 🎉
                            </p>
                            <p class="mb-0 small text-muted">Bagikan pengalaman Anda agar bermanfaat bagi peserta lain.</p>
                        </div>
                        <a href="{{ route('student.review.create', $course) }}" class="btn btn-warning fw-semibold ms-auto">
                            <i class="bi bi-star me-1"></i>Beri Ulasan
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
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
                    @php
                        $isCompleted = in_array($lesson->id, $completedLessonIds);
                        $icon = match($lesson->type) {
                            'video'    => 'bi-play-circle text-primary',
                            'quiz'     => 'bi-patch-question text-warning',
                            'document' => 'bi-file-earmark-text text-secondary',
                            default    => 'bi-file-text text-secondary',
                        };
                    @endphp
                    <a href="{{ route('student.lesson', $lesson) }}"
                       class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top {{ $isCompleted ? 'bg-success bg-opacity-5' : '' }}"
                       style="color:inherit;">
                        <i class="bi {{ $isCompleted ? 'bi-check-circle-fill text-success' : $icon }}" style="font-size:14px;"></i>
                        <span class="flex-grow-1" style="font-size:13px;">{{ $lesson->title }}</span>
                        @if($isCompleted)
                            <span class="badge bg-success rounded-pill" style="font-size:10px;">✓</span>
                        @elseif($lesson->type === 'quiz')
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size:10px;">Kuis</span>
                        @elseif($lesson->duration)
                            <span class="text-muted" style="font-size:11px;">{{ $lesson->formatted_duration }}</span>
                        @endif
                    </a>
                    @endforeach

                    <!-- Standalone quizzes (not embedded in lessons) -->
                    @php $lessonQuizIds = $section->lessons->pluck('quiz_id')->filter()->toArray(); @endphp
                    @foreach($section->quizzes->whereNotIn('id', $lessonQuizIds) as $quiz)
                    <a href="{{ route('student.quiz.start', $quiz) }}"
                       class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top" style="color:inherit;">
                        <i class="bi bi-patch-question text-warning" style="font-size:14px;"></i>
                        <span style="font-size:13px;">Quiz: {{ $quiz->title }}</span>
                        <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size:10px;">Quiz</span>
                    </a>
                    @endforeach

                    <!-- Assignments in section -->
                    @foreach($section->assignments as $assignment)
                    @php
                        $isGraded    = in_array($assignment->id, $gradedAssignmentIds ?? []);
                        $isSubmitted = in_array($assignment->id, $submittedAssignmentIds ?? []);
                    @endphp
                    <a href="{{ route('student.assignment', $assignment) }}"
                       class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top
                              {{ $isGraded ? 'bg-success bg-opacity-5' : ($isSubmitted ? 'bg-primary bg-opacity-5' : '') }}"
                       style="color:inherit;">
                        <i class="bi {{ $isGraded ? 'bi-check-circle-fill text-success' : 'bi-clipboard2-check' }}
                                  {{ !$isGraded && $isSubmitted ? 'text-primary' : (!$isGraded ? 'text-danger' : '') }}"
                           style="font-size:14px;min-width:16px;"></i>
                        <span style="font-size:13px;">Tugas: {{ $assignment->title }}</span>
                        @if($isGraded)
                            <span class="badge bg-success rounded-pill ms-auto" style="font-size:10px;">✓ Dinilai</span>
                        @elseif($isSubmitted)
                            <span class="badge bg-primary rounded-pill ms-auto" style="font-size:10px;">Dikumpulkan</span>
                        @endif
                    </a>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

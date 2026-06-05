@extends('layouts.app')
@section('title', $lesson->title)

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Main Content -->
        <div class="col-lg-8">

            {{-- ── VIDEO AREA ────────────────────────────────────── --}}
            @if($lesson->type === 'video' && $lesson->video_url)
            @php
                $url = $lesson->video_url;
                $isYoutube = str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be');
                if ($isYoutube) {
                    preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]+)/', $url, $m);
                    $videoId  = $m[1] ?? '';
                    $embedUrl = 'https://www.youtube.com/embed/' . $videoId . '?enablejsapi=1&rel=0';
                } else {
                    $isYoutube = false;
                    $embedUrl  = $url;
                }
            @endphp

            @if($isYoutube)
            <div class="ratio ratio-16x9 bg-black">
                <iframe id="yt-player"
                        src="{{ $embedUrl }}"
                        allow="autoplay; encrypted-media"
                        allowfullscreen>
                </iframe>
            </div>
            @else
            {{-- HTML5 video or other embed --}}
            <div class="ratio ratio-16x9 bg-black">
                <video id="html5-player" src="{{ $embedUrl }}" controls style="width:100%;height:100%;"></video>
            </div>
            @endif

            @else
            {{-- Non-video lesson --}}
            <div class="bg-light p-4" style="min-height:350px;">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center">
                        <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
                        <p class="text-muted mt-2">{{ ucfirst($lesson->type) }} Materi</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── LESSON INFO ──────────────────────────────────── --}}
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('student.learn', $course) }}">{{ Str::limit($course->title, 30) }}</a>
                                </li>
                                <li class="breadcrumb-item active">{{ Str::limit($lesson->title, 40) }}</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-0">{{ $lesson->title }}</h4>
                    </div>

                    <div id="complete-area">
                        @if(!$progress->completed_at)
                        <button id="btn-complete"
                                class="btn btn-success btn-sm"
                                data-lesson-id="{{ $lesson->id }}"
                                data-complete-url="{{ route('student.lesson.complete', $lesson) }}"
                                data-next-url="{{ $nextLesson ? route('student.lesson', $nextLesson) : '' }}">
                            <i class="bi bi-check-circle me-1"></i>Tandai Selesai
                        </button>
                        @else
                        <span class="badge bg-success py-2 px-3" id="badge-done">
                            <i class="bi bi-check-circle-fill me-1"></i>Selesai
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Auto-next countdown (hidden by default) --}}
                <div id="autonext-bar" class="alert alert-success d-none py-2 px-3 d-flex align-items-center gap-3">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <span>Pelajaran selesai! Lanjut ke video berikutnya dalam <strong id="countdown">5</strong> detik…</span>
                    <a id="autonext-link" href="#" class="btn btn-sm btn-success ms-auto">Lanjut Sekarang</a>
                    <button class="btn btn-sm btn-outline-secondary" onclick="cancelAutoNext()">Batal</button>
                </div>

                @if($lesson->content)
                <div class="prose mt-3">{!! nl2br(e($lesson->content)) !!}</div>
                @endif

                {{-- Downloadable materials --}}
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

                {{-- Prev / Next buttons --}}
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

        {{-- ── SIDEBAR CURRICULUM ────────────────────────────────── --}}
        <div class="col-lg-4 border-start d-none d-lg-block" style="height:calc(100vh - 70px);overflow-y:auto;">
            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Kurikulum</h6>
                <small class="text-muted">{{ round($enrollment->progress_percentage) }}% selesai</small>
            </div>

            @php $completedSet = collect($completedLessonIds); @endphp

            @foreach($course->sections as $section)
            <div class="border-bottom">
                <div class="px-3 py-2 bg-white fw-semibold small text-muted text-uppercase" style="font-size:11px;letter-spacing:.5px;">
                    {{ $section->title }}
                </div>
                @foreach($section->lessons as $l)
                @php
                    $isDone    = $completedSet->contains($l->id);
                    $isCurrent = $l->id === $lesson->id;
                @endphp
                <a href="{{ route('student.lesson', $l) }}"
                   id="sidebar-lesson-{{ $l->id }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top
                          {{ $isCurrent ? 'bg-primary bg-opacity-10' : ($isDone ? 'bg-success bg-opacity-5' : '') }}"
                   style="color:inherit;">

                    {{-- icon --}}
                    <i class="bi {{ $isDone ? 'bi-check-circle-fill text-success' : ($l->type === 'video' ? 'bi-play-circle' : 'bi-file-text') }}
                              {{ !$isDone && $isCurrent ? 'text-primary' : (!$isDone ? 'text-muted' : '') }}"
                       style="font-size:14px;min-width:16px;"></i>

                    {{-- title --}}
                    <span class="flex-grow-1 {{ $isCurrent ? 'fw-semibold text-primary' : '' }}"
                          style="font-size:13px;">{{ $l->title }}</span>

                    {{-- duration / badge --}}
                    @if($isDone)
                        <span class="badge bg-success rounded-pill" style="font-size:10px;">✓</span>
                    @elseif($l->duration)
                        <span class="text-muted" style="font-size:11px;">{{ $l->formatted_duration }}</span>
                    @endif
                </a>
                @endforeach

                @foreach($section->quizzes as $quiz)
                <a href="{{ route('student.quiz.start', $quiz) }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top" style="color:inherit;">
                    <i class="bi bi-patch-question text-warning" style="font-size:14px;min-width:16px;"></i>
                    <span style="font-size:13px;">Quiz: {{ $quiz->title }}</span>
                </a>
                @endforeach

                @foreach($section->assignments as $assignment)
                <a href="{{ route('student.assignment', $assignment) }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top" style="color:inherit;">
                    <i class="bi bi-clipboard2-check text-danger" style="font-size:14px;min-width:16px;"></i>
                    <span style="font-size:13px;">Tugas: {{ $assignment->title }}</span>
                </a>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── JAVASCRIPT ──────────────────────────────────────────────────── --}}
<script>
const LESSON_ID      = {{ $lesson->id }};
const COMPLETE_URL   = "{{ route('student.lesson.complete', $lesson) }}";
const NEXT_URL       = "{{ $nextLesson ? route('student.lesson', $nextLesson) : '' }}";
const IS_VIDEO       = {{ $lesson->type === 'video' ? 'true' : 'false' }};
const IS_YOUTUBE     = {{ isset($isYoutube) && $isYoutube ? 'true' : 'false' }};
const CSRF           = "{{ csrf_token() }}";
let   alreadyDone    = {{ $progress->completed_at ? 'true' : 'false' }};
let   autoNextTimer  = null;

// ── Mark lesson complete via AJAX ─────────────────────────────────
async function markComplete() {
    if (alreadyDone) return;
    alreadyDone = true;

    try {
        const res  = await fetch(COMPLETE_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();

        // Update sidebar: replace icon & bg for current lesson
        updateSidebarItem(LESSON_ID);

        // Swap button → badge
        const area = document.getElementById('complete-area');
        if (area) {
            area.innerHTML = `<span class="badge bg-success py-2 px-3"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span>`;
        }

        // Show auto-next bar if there is a next lesson
        if (data.next_url) {
            showAutoNext(data.next_url);
        }
    } catch (e) {
        console.error('markComplete failed', e);
        alreadyDone = false;
    }
}

// ── Update sidebar item to "completed" state ──────────────────────
function updateSidebarItem(lessonId) {
    const row = document.getElementById('sidebar-lesson-' + lessonId);
    if (!row) return;

    // Background
    row.classList.remove('bg-primary', 'bg-opacity-10');
    row.classList.add('bg-success', 'bg-opacity-5');

    // Icon: swap to check-circle-fill
    const icon = row.querySelector('i.bi');
    if (icon) {
        icon.className = 'bi bi-check-circle-fill text-success';
        icon.style.fontSize = '14px';
        icon.style.minWidth = '16px';
    }

    // Duration span → green badge
    const durationSpan = row.querySelector('span.text-muted');
    if (durationSpan) {
        durationSpan.outerHTML = `<span class="badge bg-success rounded-pill" style="font-size:10px;">✓</span>`;
    } else {
        // Add badge if not there yet
        const badge = document.createElement('span');
        badge.className = 'badge bg-success rounded-pill';
        badge.style.fontSize = '10px';
        badge.textContent = '✓';
        row.appendChild(badge);
    }
}

// ── Auto-next countdown ───────────────────────────────────────────
function showAutoNext(nextUrl) {
    const bar  = document.getElementById('autonext-bar');
    const link = document.getElementById('autonext-link');
    if (!bar || !nextUrl) return;

    link.href = nextUrl;
    bar.classList.remove('d-none');

    let secs = 5;
    document.getElementById('countdown').textContent = secs;

    autoNextTimer = setInterval(() => {
        secs--;
        const el = document.getElementById('countdown');
        if (el) el.textContent = secs;
        if (secs <= 0) {
            clearInterval(autoNextTimer);
            window.location.href = nextUrl;
        }
    }, 1000);
}

function cancelAutoNext() {
    clearInterval(autoNextTimer);
    const bar = document.getElementById('autonext-bar');
    if (bar) bar.classList.add('d-none');
}

// ── Manual "Tandai Selesai" button ────────────────────────────────
const btnComplete = document.getElementById('btn-complete');
if (btnComplete) {
    btnComplete.addEventListener('click', markComplete);
}

// ── YouTube IFrame API ────────────────────────────────────────────
@if(isset($isYoutube) && $isYoutube)
    window.onYouTubeIframeAPIReady = function () {
        new YT.Player('yt-player', {
            events: {
                onStateChange: function (e) {
                    // YT.PlayerState.ENDED = 0
                    if (e.data === 0) markComplete();
                }
            }
        });
    };
    // Load YouTube IFrame API
    (function () {
        const tag = document.createElement('script');
        tag.src   = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(tag);
    })();
@endif

// ── HTML5 video ended ─────────────────────────────────────────────
@if($lesson->type === 'video' && !(isset($isYoutube) && $isYoutube))
    const html5Video = document.getElementById('html5-player');
    if (html5Video) {
        html5Video.addEventListener('ended', markComplete);
    }
@endif

// ── Non-video lessons: mark complete automatically after load ─────
@if($lesson->type !== 'video')
    if (!alreadyDone) {
        // Give student a moment to see the content, then auto-complete
        setTimeout(markComplete, 3000);
    }
@endif
</script>
@endsection

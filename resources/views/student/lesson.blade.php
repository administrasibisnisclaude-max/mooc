@extends('layouts.app')
@section('title', $lesson->title)

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0">

        {{-- ══════════════════════════════════════════════════════════
             MAIN CONTENT
        ══════════════════════════════════════════════════════════ --}}
        <div class="col-lg-8">

            {{-- ── VIDEO ─────────────────────────────────────── --}}
            @if($lesson->type === 'video' && $lesson->video_url)
            @php
                $url = $lesson->video_url;
                $isYoutube = str_contains($url,'youtube.com') || str_contains($url,'youtu.be');
                if ($isYoutube) {
                    preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]+)/',$url,$m);
                    $videoId  = $m[1] ?? '';
                    $embedUrl = 'https://www.youtube.com/embed/'.$videoId.'?enablejsapi=1&rel=0';
                }
            @endphp
            @if($isYoutube)
            <div class="ratio ratio-16x9 bg-black">
                <iframe id="yt-player" src="{{ $embedUrl }}" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
            @else
            <div class="ratio ratio-16x9 bg-black">
                <video id="html5-player" src="{{ $lesson->video_url }}" controls style="width:100%;height:100%;"></video>
            </div>
            @endif

            {{-- ── QUIZ INLINE ───────────────────────────────── --}}
            @elseif($lesson->type === 'quiz' && $lesson->quiz)
            @php
                $quiz        = $lesson->quiz->load('questions.options');
                $quizResult  = session('quiz_result');
                $isCompleted = $progress->completed_at !== null;
            @endphp
            <div class="bg-primary bg-opacity-10 border-bottom p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle text-white"
                         style="width:48px;height:48px;min-width:48px;">
                        <i class="bi bi-patch-question fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $quiz->title }}</h5>
                        <span class="text-muted small">
                            {{ $quiz->questions->count() }} soal &bull;
                            Passing score: {{ $quiz->passing_score }}%
                            @if($quiz->time_limit) &bull; {{ $quiz->time_limit }} menit @endif
                        </span>
                    </div>
                    @if($isCompleted)
                    <span class="badge bg-success ms-auto py-2 px-3">
                        <i class="bi bi-check-circle-fill me-1"></i>Lulus
                    </span>
                    @endif
                </div>
            </div>

            {{-- ── Quiz Result Banner ──────────────────────── --}}
            @if($quizResult)
            <div class="p-4">
                @if($quizResult['passed'])
                <div class="alert alert-success border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-trophy-fill text-warning fs-2"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Selamat! Anda Lulus! 🎉</h5>
                            <p class="mb-0">Skor: <strong>{{ $quizResult['score'] }}%</strong> &bull;
                               Benar: <strong>{{ $quizResult['correct'] }}/{{ $quizResult['total'] }}</strong> soal</p>
                        </div>
                    </div>
                    @if($quizResult['next_url'])
                    <a href="{{ $quizResult['next_url'] }}" class="btn btn-success">
                        <i class="bi bi-arrow-right me-1"></i>Lanjut ke Materi Berikutnya
                    </a>
                    @endif
                </div>
                @else
                <div class="alert alert-danger border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-x-circle-fill text-danger fs-2"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Belum Lulus</h5>
                            <p class="mb-0">Skor: <strong>{{ $quizResult['score'] }}%</strong>
                               (minimal {{ $quizResult['passing'] }}%) &bull;
                               Benar: <strong>{{ $quizResult['correct'] }}/{{ $quizResult['total'] }}</strong> soal</p>
                        </div>
                    </div>
                    <p class="mb-2 small">Pelajari kembali materi dan coba lagi.</p>
                    <a href="#quiz-form" class="btn btn-danger btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                    </a>
                </div>
                @endif
            </div>
            @endif

            {{-- ── Quiz Form ──────────────────────────────── --}}
            @if(!$isCompleted || !($quizResult['passed'] ?? false))
            <div class="p-4" id="quiz-form">
                <form action="{{ route('student.lesson.quiz.submit', $lesson) }}" method="POST" id="inlineQuizForm">
                    @csrf

                    @if($quiz->time_limit && !$isCompleted)
                    <div class="d-flex align-items-center gap-2 mb-4 p-3 bg-warning bg-opacity-10 rounded">
                        <i class="bi bi-clock text-warning fs-5"></i>
                        <span class="fw-semibold">Waktu: <span id="quiz-timer" class="text-danger">{{ $quiz->time_limit }}:00</span></span>
                    </div>
                    @endif

                    @foreach($quiz->questions as $qi => $question)
                    <div class="mb-5">
                        <p class="fw-semibold mb-3" style="font-size:15px;">
                            <span class="badge bg-primary me-2">{{ $qi + 1 }}</span>
                            {{ $question->question }}
                        </p>
                        <div class="d-flex flex-column gap-2 ps-4">
                            @foreach($question->options as $oi => $option)
                            <label class="quiz-option d-flex align-items-center gap-3 p-3 border rounded"
                                   for="opt_{{ $option->id }}" style="cursor:pointer;">
                                <input type="radio"
                                       name="answers[{{ $question->id }}]"
                                       value="{{ $option->id }}"
                                       id="opt_{{ $option->id }}"
                                       class="form-check-input mt-0 flex-shrink-0"
                                       {{ $isCompleted ? 'disabled' : '' }}>
                                <span class="d-flex align-items-center justify-content-center fw-bold text-muted border rounded"
                                      style="width:28px;height:28px;min-width:28px;font-size:13px;">
                                    {{ chr(65 + $oi) }}
                                </span>
                                <span>{{ $option->option_text }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if(!$isCompleted)
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <span class="text-muted small">
                            {{ $quiz->questions->count() }} soal &bull; Passing: {{ $quiz->passing_score }}%
                        </span>
                        <button type="submit" class="btn btn-primary px-4"
                                onclick="return confirm('Yakin ingin mengumpulkan jawaban?')">
                            <i class="bi bi-send me-1"></i>Kumpulkan Jawaban
                        </button>
                    </div>
                    @endif
                </form>
            </div>
            @endif

            {{-- ── DOC / TEXT ─────────────────────────────── --}}
            @else
            <div class="bg-light p-4" style="min-height:280px;">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center">
                        <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
                        <p class="text-muted mt-2">{{ ucfirst($lesson->type) }} Materi</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── INFO & CONTROLS ───────────────────────── --}}
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('student.learn', $course) }}">{{ Str::limit($course->title,30) }}</a>
                                </li>
                                <li class="breadcrumb-item active">{{ Str::limit($lesson->title,40) }}</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-0">{{ $lesson->title }}</h4>
                    </div>

                    {{-- Complete button (only for non-quiz; quiz auto-completes on pass) --}}
                    @if($lesson->type !== 'quiz')
                    <div id="complete-area">
                        @if(!$progress->completed_at)
                        <button id="btn-complete"
                                class="btn btn-success btn-sm"
                                data-complete-url="{{ route('student.lesson.complete', $lesson) }}"
                                data-next-url="{{ $nextLesson ? route('student.lesson',$nextLesson) : '' }}">
                            <i class="bi bi-check-circle me-1"></i>Tandai Selesai
                        </button>
                        @else
                        <span class="badge bg-success py-2 px-3" id="badge-done">
                            <i class="bi bi-check-circle-fill me-1"></i>Selesai
                        </span>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Auto-next bar --}}
                <div id="autonext-bar" class="alert alert-success d-none py-2 px-3 d-flex align-items-center gap-3 flex-wrap">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <span>Selesai! Lanjut ke materi berikutnya dalam <strong id="countdown">5</strong> detik…</span>
                    <a id="autonext-link" href="#" class="btn btn-sm btn-success ms-auto">Lanjut Sekarang</a>
                    <button class="btn btn-sm btn-outline-secondary" onclick="cancelAutoNext()">Batal</button>
                </div>

                @if($lesson->content && $lesson->type !== 'quiz')
                <div class="prose mt-3">{!! nl2br(e($lesson->content)) !!}</div>
                @endif

                {{-- Downloadable materials --}}
                @if($lesson->materials->count())
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Materi Pendukung</h6>
                    @foreach($lesson->materials as $material)
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded mb-2">
                        <i class="bi bi-file-earmark text-primary fs-4"></i>
                        <div class="flex-grow-1">
                            <strong class="small">{{ $material->title }}</strong>
                            <p class="mb-0 text-muted" style="font-size:11px;">{{ $material->file_type }} &bull; {{ $material->formatted_size }}</p>
                        </div>
                        <a href="{{ asset('storage/'.$material->file_path) }}" class="btn btn-sm btn-outline-primary" download>
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Prev / Next navigation --}}
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    @if($prevLesson)
                    <a href="{{ route('student.lesson',$prevLesson) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                    </a>
                    @else<span></span>@endif
                    @if($nextLesson)
                    <a href="{{ route('student.lesson',$nextLesson) }}" class="btn btn-primary">
                        Selanjutnya <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SIDEBAR CURRICULUM
        ══════════════════════════════════════════════════════════ --}}
        <div class="col-lg-4 border-start d-none d-lg-block" style="height:calc(100vh - 70px);overflow-y:auto;">
            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Kurikulum</h6>
                <small class="text-muted">{{ round($enrollment->progress_percentage) }}% selesai</small>
            </div>

            @php $completedSet = collect($completedLessonIds); @endphp

            @foreach($course->sections as $section)
            <div class="border-bottom">
                <div class="px-3 py-2 bg-white fw-semibold text-muted text-uppercase"
                     style="font-size:11px;letter-spacing:.5px;">{{ $section->title }}</div>

                @foreach($section->lessons as $l)
                @php
                    $isDone    = $completedSet->contains($l->id);
                    $isCurrent = $l->id === $lesson->id;
                    $icon = match($l->type) {
                        'video'    => 'bi-play-circle',
                        'quiz'     => 'bi-patch-question',
                        'document' => 'bi-file-earmark-text',
                        default    => 'bi-file-text',
                    };
                    $iconColor = $isDone ? 'text-success' : ($isCurrent ? 'text-primary' : 'text-muted');
                @endphp
                <a href="{{ route('student.lesson', $l) }}"
                   id="sidebar-lesson-{{ $l->id }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top
                          {{ $isCurrent ? 'bg-primary bg-opacity-10' : ($isDone ? 'bg-success bg-opacity-5' : '') }}"
                   style="color:inherit;">

                    <i class="bi {{ $isDone ? 'bi-check-circle-fill text-success' : "$icon $iconColor" }}"
                       style="font-size:14px;min-width:16px;"></i>

                    <span class="flex-grow-1 {{ $isCurrent ? 'fw-semibold text-primary' : '' }}"
                          style="font-size:13px;">{{ $l->title }}</span>

                    @if($isDone)
                        <span class="badge bg-success rounded-pill" style="font-size:10px;">✓</span>
                    @elseif($l->type === 'quiz')
                        <span class="badge bg-warning text-dark rounded-pill" style="font-size:10px;">Kuis</span>
                    @elseif($l->duration)
                        <span class="text-muted" style="font-size:11px;">{{ $l->formatted_duration }}</span>
                    @endif
                </a>
                @endforeach

                {{-- Section quizzes (standalone — not embedded in a lesson) --}}
                @php
                    $lessonQuizIds = $section->lessons->pluck('quiz_id')->filter()->toArray();
                @endphp
                @foreach($section->quizzes->whereNotIn('id', $lessonQuizIds) as $standaloneQuiz)
                <a href="{{ route('student.quiz.start', $standaloneQuiz) }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none border-top" style="color:inherit;">
                    <i class="bi bi-patch-question text-warning" style="font-size:14px;min-width:16px;"></i>
                    <span style="font-size:13px;">Quiz: {{ $standaloneQuiz->title }}</span>
                    <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size:10px;">Quiz</span>
                </a>
                @endforeach

                {{-- Assignments --}}
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

{{-- ══════════════════════════════════════════════════════════════════
     STYLES
══════════════════════════════════════════════════════════════════ --}}
<style>
.quiz-option { transition: all .15s; }
.quiz-option:hover { background: #f0f7ff; border-color: #0056D2 !important; }
.quiz-option:has(input:checked) { background: #e8f0fe; border-color: #0056D2 !important; }
</style>

{{-- ══════════════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════════════ --}}
<script>
const COMPLETE_URL = "{{ route('student.lesson.complete', $lesson) }}";
const NEXT_URL     = "{{ $nextLesson ? route('student.lesson', $nextLesson) : '' }}";
const IS_VIDEO     = {{ $lesson->type === 'video' ? 'true' : 'false' }};
const CSRF         = "{{ csrf_token() }}";
let   alreadyDone  = {{ $progress->completed_at ? 'true' : 'false' }};
let   autoNextTimer = null;

// ── Mark complete via AJAX (video/doc/text only) ──────────────────
async function markComplete() {
    if (alreadyDone) return;
    alreadyDone = true;

    try {
        const res  = await fetch(COMPLETE_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();

        updateSidebarItem({{ $lesson->id }});

        const area = document.getElementById('complete-area');
        if (area) {
            area.innerHTML = `<span class="badge bg-success py-2 px-3">
                <i class="bi bi-check-circle-fill me-1"></i>Selesai</span>`;
        }

        if (data.next_url) showAutoNext(data.next_url);
    } catch (e) {
        alreadyDone = false;
    }
}

function updateSidebarItem(id) {
    const row = document.getElementById('sidebar-lesson-' + id);
    if (!row) return;
    row.classList.remove('bg-primary','bg-opacity-10');
    row.classList.add('bg-success','bg-opacity-5');
    const icon = row.querySelector('i.bi');
    if (icon) { icon.className = 'bi bi-check-circle-fill text-success'; icon.style.fontSize = '14px'; icon.style.minWidth = '16px'; }
    const dur  = row.querySelector('span.text-muted');
    const quiz = row.querySelector('.badge.bg-warning');
    const el   = dur || quiz;
    if (el) el.outerHTML = `<span class="badge bg-success rounded-pill" style="font-size:10px;">✓</span>`;
}

function showAutoNext(url) {
    const bar  = document.getElementById('autonext-bar');
    const link = document.getElementById('autonext-link');
    if (!bar || !url) return;
    link.href = url;
    bar.classList.remove('d-none');
    let secs = 5;
    document.getElementById('countdown').textContent = secs;
    autoNextTimer = setInterval(() => {
        secs--;
        const el = document.getElementById('countdown');
        if (el) el.textContent = secs;
        if (secs <= 0) { clearInterval(autoNextTimer); window.location.href = url; }
    }, 1000);
}

function cancelAutoNext() {
    clearInterval(autoNextTimer);
    const bar = document.getElementById('autonext-bar');
    if (bar) bar.classList.add('d-none');
}

// ── Manual button ─────────────────────────────────────────────────
const btnComplete = document.getElementById('btn-complete');
if (btnComplete) {
    btnComplete.addEventListener('click', markComplete);
}

// ── YouTube API ───────────────────────────────────────────────────
@if($lesson->type === 'video' && isset($isYoutube) && $isYoutube)
window.onYouTubeIframeAPIReady = function() {
    new YT.Player('yt-player', {
        events: { onStateChange: e => { if (e.data === 0) markComplete(); } }
    });
};
(function(){ const t=document.createElement('script'); t.src='https://www.youtube.com/iframe_api'; document.head.appendChild(t); })();
@endif

// ── HTML5 video ───────────────────────────────────────────────────
@if($lesson->type === 'video' && !(isset($isYoutube) && $isYoutube))
const v = document.getElementById('html5-player');
if (v) v.addEventListener('ended', markComplete);
@endif

// ── Non-video, non-quiz: auto-complete after 3s ───────────────────
@if(!in_array($lesson->type, ['video','quiz']))
if (!alreadyDone) setTimeout(markComplete, 3000);
@endif

// ── Quiz timer ────────────────────────────────────────────────────
@if($lesson->type === 'quiz' && $lesson->quiz && $lesson->quiz->time_limit && !$progress->completed_at)
let qMin = {{ $lesson->quiz->time_limit }}, qSec = 0;
const timerEl = document.getElementById('quiz-timer');
const qInterval = setInterval(() => {
    qSec--;
    if (qSec < 0) { qMin--; qSec = 59; }
    if (qMin < 0) { clearInterval(qInterval); document.getElementById('inlineQuizForm')?.submit(); return; }
    if (timerEl) {
        timerEl.textContent = `${String(qMin).padStart(2,'0')}:${String(qSec).padStart(2,'0')}`;
        timerEl.className = qMin < 2 ? 'text-danger fw-bold' : 'text-danger';
    }
}, 1000);
@endif
</script>
@endsection

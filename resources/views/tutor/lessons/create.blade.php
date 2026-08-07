@extends('layouts.tutor')
@section('title', 'Tambah Materi')
@section('page-title', 'Tambah Materi')

@section('content')
<div class="card" style="max-width:780px;">
    <div class="card-header bg-white">
        <p class="text-muted small mb-0">Bagian: <strong>{{ $section->title }}</strong> &bull; Kursus: <strong>{{ $section->course->title }}</strong></p>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('tutor.lessons.store', $section) }}" method="POST" id="lessonForm">
            @csrf

            {{-- ── Basic fields ─────────────────────────────── --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Materi <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tipe Materi</label>
                <select name="type" class="form-select" id="lessonType">
                    <option value="video"    {{ old('type')=='video'    ? 'selected':'' }}>Video</option>
                    <option value="document" {{ old('type')=='document' ? 'selected':'' }}>Dokumen</option>
                    <option value="text"     {{ old('type')=='text'     ? 'selected':'' }}>Teks</option>
                    <option value="quiz"     {{ old('type')=='quiz'     ? 'selected':'' }}>Kuis Pilihan Ganda</option>
                </select>
            </div>

            {{-- ── Video field ──────────────────────────────── --}}
            <div id="videoFields" class="mb-3">
                <label class="form-label fw-semibold">URL Video</label>
                <input type="text" name="video_url" class="form-control" value="{{ old('video_url') }}"
                       placeholder="https://youtube.com/watch?v=...">
                <small class="text-muted">YouTube atau URL video langsung</small>
            </div>

            {{-- ── Content / description (non-quiz) ───────── --}}
            <div id="contentFields" class="mb-3">
                <label class="form-label fw-semibold">Deskripsi / Konten</label>
                <textarea name="content" class="form-control" rows="5"
                          placeholder="Deskripsi materi...">{{ old('content') }}</textarea>
            </div>

            {{-- ── Duration + free preview (non-quiz) ──────── --}}
            <div id="metaFields" class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Durasi (menit)</label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration',0) }}" min="0">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="is_free_preview" id="is_free_preview" value="1"
                               {{ old('is_free_preview') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_free_preview">Preview Gratis</label>
                    </div>
                </div>
            </div>

            {{-- ── QUIZ SECTION ──────────────────────────────── --}}
            <div id="quizFields" class="d-none">
                <hr>
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-patch-question me-2"></i>Pengaturan Kuis</h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Passing Score (%)</label>
                        <input type="number" name="passing_score" class="form-control"
                               value="{{ old('passing_score', 70) }}" min="1" max="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Batas Waktu <small class="text-muted">(menit, opsional)</small></label>
                        <input type="number" name="time_limit" class="form-control"
                               value="{{ old('time_limit') }}" min="1" placeholder="Tanpa batas">
                    </div>
                </div>

                {{-- Question list --}}
                <div id="questionList"></div>

                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addQuestion()">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Soal
                </button>
                <p class="text-muted small mt-2 mb-0">Klik "Tambah Soal" untuk menambah pertanyaan pilihan ganda.</p>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Materi</button>
                <a href="{{ route('tutor.courses.show', $section->course_id) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let questionCount = 0;

const LABELS = ['A', 'B', 'C', 'D', 'E'];

function toggleFields() {
    const type = document.getElementById('lessonType').value;
    const isQuiz = type === 'quiz';
    const isVideo = type === 'video';

    document.getElementById('videoFields').classList.toggle('d-none', !isVideo);
    document.getElementById('contentFields').classList.toggle('d-none', isQuiz);
    document.getElementById('metaFields').classList.toggle('d-none', isQuiz);
    document.getElementById('quizFields').classList.toggle('d-none', !isQuiz);

    // Auto-add first question
    if (isQuiz && questionCount === 0) addQuestion();
}

function addQuestion() {
    const idx = questionCount++;
    const div = document.createElement('div');
    div.className = 'card border mb-3';
    div.id = `q_${idx}`;

    const options = LABELS.slice(0, 4).map((lbl, j) => `
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="d-flex align-items-center justify-content-center fw-bold text-muted"
                 style="width:28px;height:28px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;">
                ${lbl}
            </div>
            <input type="text" name="questions[${idx}][options][${j}]"
                   class="form-control form-control-sm" placeholder="Opsi ${lbl}" required>
            <div class="form-check mb-0">
                <input class="form-check-input" type="radio" name="questions[${idx}][correct]"
                       value="${j}" id="correct_${idx}_${j}" required>
                <label class="form-check-label small text-success fw-semibold" for="correct_${idx}_${j}">Benar</label>
            </div>
        </div>
    `).join('');

    div.innerHTML = `
        <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
            <span class="fw-semibold small">Soal #<span class="q-num">${idx + 1}</span></span>
            <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeQuestion('q_${idx}')">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
        <div class="card-body p-3">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Pertanyaan</label>
                <textarea name="questions[${idx}][question]" class="form-control form-control-sm"
                          rows="2" placeholder="Tulis pertanyaan..." required></textarea>
            </div>
            <label class="form-label small fw-semibold">Opsi Jawaban <span class="text-muted">(pilih satu yang benar)</span></label>
            ${options}
            <button type="button" class="btn btn-link btn-sm text-primary p-0 mt-1" onclick="addOption(this, ${idx})">
                <i class="bi bi-plus"></i> Tambah Opsi
            </button>
        </div>
    `;
    document.getElementById('questionList').appendChild(div);
    renumberQuestions();
}

function addOption(btn, idx) {
    const container = btn.previousElementSibling.previousElementSibling;
    const count = container.querySelectorAll('input[type=text]').length;
    if (count >= 5) { alert('Maksimal 5 opsi per soal.'); return; }
    const lbl = LABELS[count];
    const row = document.createElement('div');
    row.className = 'd-flex align-items-center gap-2 mb-2';
    row.innerHTML = `
        <div class="d-flex align-items-center justify-content-center fw-bold text-muted"
             style="width:28px;height:28px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;">${lbl}</div>
        <input type="text" name="questions[${idx}][options][${count}]"
               class="form-control form-control-sm" placeholder="Opsi ${lbl}">
        <div class="form-check mb-0">
            <input class="form-check-input" type="radio" name="questions[${idx}][correct]"
                   value="${count}" id="correct_${idx}_${count}">
            <label class="form-check-label small text-success fw-semibold" for="correct_${idx}_${count}">Benar</label>
        </div>`;
    btn.before(row);
}

function removeQuestion(id) {
    const el = document.getElementById(id);
    if (el) { el.remove(); renumberQuestions(); }
}

function renumberQuestions() {
    document.querySelectorAll('#questionList .card').forEach((card, i) => {
        const num = card.querySelector('.q-num');
        if (num) num.textContent = i + 1;
    });
}

document.getElementById('lessonType').addEventListener('change', toggleFields);
toggleFields(); // init
</script>
@endpush
@endsection

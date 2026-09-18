@extends('layouts.tutor')
@section('title', 'Buat Challenge')
@section('page-title', 'Buat Challenge Baru')

@push('styles')
<style>
.level-card { border:1px solid #e9ecef; border-radius:10px; margin-bottom:16px; }
.level-card .card-header { background:#f8f9fa; border-bottom:1px solid #e9ecef; border-radius:10px 10px 0 0; }
.step-badge { width:28px;height:28px;border-radius:50%;background:#0d6efd;color:#fff;font-size:12px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0; }
.sticky-sidebar { position:sticky; top:80px; }
.puzzle-section { border:1px solid #dee2e6; border-radius:8px; padding:16px; background:#fafafa; }
</style>
@endpush

@section('content')

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('tutor.courses.show', $course) }}" class="text-decoration-none">{{ Str::limit($course->title, 30) }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('tutor.challenges.index', $course) }}" class="text-decoration-none">Challenges</a></li>
        <li class="breadcrumb-item active">Buat Baru</li>
    </ol>
</nav>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i><strong>Perbaiki kesalahan berikut:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form id="challengeForm" action="{{ route('tutor.challenges.store', $course) }}" method="POST">
@csrf

<div class="row g-4">

    {{-- ── Sidebar: Info Challenge ──────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card sticky-sidebar">
            <div class="card-header d-flex align-items-center gap-2 fw-bold">
                <span class="step-badge">1</span> Info Challenge
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="Contoh: PHP Problem Solving" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Jelaskan tujuan dan manfaat challenge ini...">{{ old('description') }}</textarea>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="problem_solving" {{ old('type')=='problem_solving'?'selected':'' }}>💡 Problem Solving</option>
                            <option value="puzzle_logic" {{ old('type')=='puzzle_logic'?'selected':'' }}>🧩 Puzzle Logic</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kesulitan <span class="text-danger">*</span></label>
                        <select name="difficulty" class="form-select" required>
                            <option value="easy" {{ old('difficulty')=='easy'?'selected':'' }}>Mudah</option>
                            <option value="medium" selected {{ old('difficulty')=='medium'?'selected':'' }}>Sedang</option>
                            <option value="hard" {{ old('difficulty')=='hard'?'selected':'' }}>Sulit</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Waktu (mnt) <span class="text-danger">*</span></label>
                        <input type="number" name="time_limit" class="form-control @error('time_limit') is-invalid @enderror"
                               value="{{ old('time_limit', 30) }}" min="1" max="180" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Poin/Level <span class="text-danger">*</span></label>
                        <input type="number" name="points_per_level" class="form-control @error('points_per_level') is-invalid @enderror"
                               value="{{ old('points_per_level', 10) }}" min="1" max="100" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Icon (emoji)</label>
                    <input type="text" name="icon" class="form-control" value="{{ old('icon','🎯') }}" maxlength="10">
                </div>
                <hr>
                <button type="button" class="btn btn-outline-primary w-100 mb-2" onclick="addLevel()">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Level
                </button>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Challenge
                    </button>
                </div>
                <div id="levelCounter" class="text-center text-muted small mt-2">0 level ditambahkan</div>
            </div>
        </div>
    </div>

    {{-- ── Main: Levels ─────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="step-badge">2</span>
            <h6 class="fw-bold mb-0">Tambahkan Level / Soal</h6>
        </div>

        <div id="levelsContainer"></div>

        <div id="emptyMsg" class="text-center text-muted py-5 border border-dashed rounded-3">
            <i class="bi bi-layers" style="font-size:2.5rem;"></i>
            <p class="mt-2 mb-3">Belum ada level. Klik <strong>"Tambah Level"</strong> untuk menambahkan soal.</p>
            <button type="button" class="btn btn-primary" onclick="addLevel()"><i class="bi bi-plus me-1"></i>Tambah Level Pertama</button>
        </div>
    </div>
</div>
</form>

{{-- ── Level Template ─────────────────────────────────────────────── --}}
<template id="levelTpl">
<div class="level-card" data-idx="__IDX__">
    <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
        <span class="fw-semibold level-label"><i class="bi bi-layers me-1 text-primary"></i>Level __NUM__</span>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLevel(this)">
            <i class="bi bi-trash me-1"></i>Hapus Level
        </button>
    </div>
    <div class="card-body p-3">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Judul Level <span class="text-danger">*</span></label>
                <input type="text" name="levels[__IDX__][title]" class="form-control" placeholder="Contoh: Variabel & Tipe Data" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tipe Soal <span class="text-danger">*</span></label>
                <select name="levels[__IDX__][puzzle_type]" class="form-select puzzle-type-sel"
                        data-idx="__IDX__" onchange="onTypeChange(this)" required>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="code_output">Code Output</option>
                    <option value="code_debug">Code Debug</option>
                    <option value="riddle">Riddle / Teka-teki</option>
                    <option value="math">Math Expression</option>
                    <option value="number_sequence">Number Sequence</option>
                    <option value="anagram">Anagram</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Instruksi / Pertanyaan <span class="text-danger">*</span></label>
            <textarea name="levels[__IDX__][instructions]" class="form-control" rows="2"
                      placeholder="Tulis instruksi yang terlihat oleh siswa..." required></textarea>
        </div>

        {{-- Dynamic extra fields --}}
        <div class="puzzle-section mb-3 puzzle-extra" data-idx="__IDX__"></div>

        {{-- Options --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Pilihan Jawaban <span class="text-danger">*</span>
                <small class="text-muted fw-normal">— centang radio = jawaban benar</small>
            </label>
            <div class="options-con" data-idx="__IDX__">
                <div class="input-group mb-2">
                    <span class="input-group-text"><input type="radio" name="levels[__IDX__][answer_index]" value="0" required></span>
                    <span class="input-group-text text-muted fw-bold">A</span>
                    <input type="text" name="levels[__IDX__][options][]" class="form-control" placeholder="Pilihan A" required>
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text"><input type="radio" name="levels[__IDX__][answer_index]" value="1" required></span>
                    <span class="input-group-text text-muted fw-bold">B</span>
                    <input type="text" name="levels[__IDX__][options][]" class="form-control" placeholder="Pilihan B" required>
                </div>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addOption(this, __IDX__)">
                <i class="bi bi-plus me-1"></i>Tambah Pilihan
            </button>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Penjelasan Jawaban <small class="text-muted fw-normal">(ditampilkan setelah menjawab)</small></label>
                <textarea name="levels[__IDX__][explanation]" class="form-control" rows="2"
                          placeholder="Jelaskan mengapa jawaban ini benar..."></textarea>
            </div>
        </div>
        <div>
            <label class="form-label fw-semibold">Hints <small class="text-muted fw-normal">(opsional, tampil saat siswa minta bantuan)</small></label>
            <div class="hints-con" data-idx="__IDX__">
                <div class="input-group mb-2">
                    <input type="text" name="levels[__IDX__][hints][]" class="form-control" placeholder="Hint 1...">
                    <button type="button" class="btn btn-outline-secondary" onclick="addHint(this, __IDX__)"><i class="bi bi-plus"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

@endsection

@push('scripts')
<script>
let levelCount = 0;

function updateCounter() {
    const n = document.querySelectorAll('.level-card').length;
    document.getElementById('levelCounter').textContent = `${n} level ditambahkan`;
}

function addLevel() {
    const idx = levelCount++;
    const num = document.querySelectorAll('.level-card').length + 1;
    const html = document.getElementById('levelTpl').innerHTML
        .replace(/__IDX__/g, idx).replace(/__NUM__/g, num);
    const wrap = document.createElement('div');
    wrap.innerHTML = html;
    document.getElementById('levelsContainer').appendChild(wrap.firstElementChild);
    document.getElementById('emptyMsg').style.display = 'none';
    renderPuzzleExtra(idx, 'multiple_choice');
    renumber();
    updateCounter();
    wrap.firstElementChild.scrollIntoView({behavior:'smooth', block:'start'});
}

function removeLevel(btn) {
    btn.closest('.level-card').remove();
    if (!document.querySelectorAll('.level-card').length)
        document.getElementById('emptyMsg').style.display = '';
    renumber();
    updateCounter();
}

function renumber() {
    document.querySelectorAll('.level-card').forEach((c, i) => {
        c.querySelector('.level-label').innerHTML = `<i class="bi bi-layers me-1 text-primary"></i>Level ${i + 1}`;
    });
}

function onTypeChange(sel) {
    renderPuzzleExtra(sel.dataset.idx, sel.value);
}

function renderPuzzleExtra(idx, type) {
    const el = document.querySelector(`.puzzle-extra[data-idx="${idx}"]`);
    let h = '';
    if (type === 'multiple_choice') {
        h = `<label class="form-label fw-semibold mb-1">Pertanyaan (Multiple Choice)</label>
             <input type="text" name="levels[${idx}][question]" class="form-control" placeholder="Tulis pertanyaan pilihan ganda...">`;
    } else if (type === 'code_output' || type === 'code_debug') {
        h = `<div class="mb-2"><label class="form-label fw-semibold mb-1">Kode Program</label>
             <textarea name="levels[${idx}][code]" class="form-control font-monospace" rows="6"
                       placeholder="// Tulis kode di sini..." style="font-size:13px;"></textarea></div>
             <label class="form-label fw-semibold mb-1">Bahasa</label>
             <select name="levels[${idx}][language]" class="form-select mb-0">
                <option value="python">Python</option><option value="javascript">JavaScript</option>
                <option value="php">PHP</option><option value="java">Java</option>
                <option value="c">C</option><option value="cpp">C++</option>
             </select>`;
        if (type === 'code_debug') {
            h += `<div class="mt-2"><label class="form-label fw-semibold mb-1">Deskripsi Bug yang Harus Ditemukan</label>
                  <input type="text" name="levels[${idx}][bug]" class="form-control" placeholder="Contoh: Division by zero saat input 0"></div>`;
        }
    } else if (type === 'riddle') {
        h = `<label class="form-label fw-semibold mb-1">Teks Teka-teki</label>
             <textarea name="levels[${idx}][riddle]" class="form-control" rows="2"
                       placeholder="Tulis teka-teki di sini..."></textarea>`;
    } else if (type === 'math') {
        h = `<label class="form-label fw-semibold mb-1">Ekspresi Matematika</label>
             <input type="text" name="levels[${idx}][expression]" class="form-control"
                    placeholder="Contoh: (15 + 5) × 3 - 10 ÷ 2 = ?">`;
    } else if (type === 'number_sequence') {
        h = `<div class="mb-2"><label class="form-label fw-semibold mb-1">
                Urutan Angka <small class="fw-normal text-muted">(koma = pemisah, kosong = blank)</small>
             </label>
             <input type="text" name="levels[${idx}][sequence]" class="form-control" placeholder="1,2,3,,5,6"></div>
             <div class="row g-2">
               <div class="col-6"><label class="form-label fw-semibold mb-1">Indeks Blank (mulai 0)</label>
               <input type="number" name="levels[${idx}][blank_index]" class="form-control" value="3" min="0"></div>
               <div class="col-6"><label class="form-label fw-semibold mb-1">Penjelasan Pola</label>
               <input type="text" name="levels[${idx}][pattern]" class="form-control" placeholder="Contoh: ×2 setiap angka"></div>
             </div>`;
    } else if (type === 'anagram') {
        h = `<div class="row g-2">
             <div class="col-6"><label class="form-label fw-semibold mb-1">Kata Diacak</label>
             <input type="text" name="levels[${idx}][scrambled]" class="form-control" placeholder="TUTARO"></div>
             <div class="col-6"><label class="form-label fw-semibold mb-1">Jawaban Kata</label>
             <input type="text" name="levels[${idx}][answer_word]" class="form-control" placeholder="TUTOR"></div>
             </div>`;
    }
    el.innerHTML = h || '<span class="text-muted small">Tidak ada field tambahan untuk tipe ini.</span>';
}

function addOption(btn, idx) {
    const con = btn.previousElementSibling;
    const count = con.querySelectorAll('.input-group').length;
    const letter = String.fromCharCode(65 + count);
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<span class="input-group-text"><input type="radio" name="levels[${idx}][answer_index]" value="${count}" required></span>
        <span class="input-group-text text-muted fw-bold">${letter}</span>
        <input type="text" name="levels[${idx}][options][]" class="form-control" placeholder="Pilihan ${letter}" required>
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove(); renumberOptions(${idx})">
            <i class="bi bi-x"></i>
        </button>`;
    con.appendChild(div);
}

function renumberOptions(idx) {
    const rows = document.querySelectorAll(`.options-con[data-idx="${idx}"] .input-group`);
    rows.forEach((r, i) => {
        r.querySelector('input[type=radio]').value = i;
        r.querySelector('.input-group-text.text-muted').textContent = String.fromCharCode(65 + i);
    });
}

function addHint(btn, idx) {
    const con = btn.closest('.hints-con');
    const count = con.querySelectorAll('.input-group').length + 1;
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<input type="text" name="levels[${idx}][hints][]" class="form-control" placeholder="Hint ${count}">
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-x"></i></button>`;
    con.appendChild(div);
}

// Validate sebelum submit
document.getElementById('challengeForm').addEventListener('submit', function(e) {
    if (!document.querySelectorAll('.level-card').length) {
        e.preventDefault();
        alert('Tambahkan minimal 1 level sebelum menyimpan challenge!');
    }
});

// Init: langsung tambah 1 level
addLevel();
</script>
@endpush

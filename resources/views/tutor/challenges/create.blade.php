@extends('layouts.tutor')
@section('title', 'Buat Challenge')
@section('page-title', 'Buat Challenge Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('tutor.challenges.index', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali ke Challenges</a>
</div>

<form id="challengeForm" action="{{ route('tutor.challenges.store', $course) }}" method="POST">
@csrf
<div class="row g-4">
    {{-- Left: Challenge Info --}}
    <div class="col-lg-4">
        <div class="card sticky-top" style="top:80px;">
            <div class="card-header fw-bold">Info Challenge</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="problem_solving">Problem Solving</option>
                        <option value="puzzle_logic">Puzzle / Logic</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kesulitan <span class="text-danger">*</span></label>
                        <select name="difficulty" class="form-select" required>
                            <option value="easy">Mudah</option>
                            <option value="medium" selected>Sedang</option>
                            <option value="hard">Sulit</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Icon (emoji)</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon','🎯') }}" maxlength="10">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Waktu (mnt) <span class="text-danger">*</span></label>
                        <input type="number" name="time_limit" class="form-control" value="{{ old('time_limit',30) }}" min="1" max="180" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Poin/Level <span class="text-danger">*</span></label>
                        <input type="number" name="points_per_level" class="form-control" value="{{ old('points_per_level',10) }}" min="1" max="100" required>
                    </div>
                </div>
                <button type="button" class="btn btn-success w-100" onclick="addLevel()"><i class="bi bi-plus me-1"></i>Tambah Level</button>
            </div>
        </div>
    </div>

    {{-- Right: Levels --}}
    <div class="col-lg-8">
        <div id="levelsContainer"></div>
        <div id="emptyMsg" class="text-center text-muted py-5">
            <i class="bi bi-layers display-4 d-block mb-2"></i>
            <p>Klik "Tambah Level" untuk menambahkan soal.</p>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('tutor.challenges.index', $course) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Simpan Challenge</button>
        </div>
    </div>
</div>
</form>

<template id="levelTemplate">
<div class="card mb-3 level-card" data-idx="__IDX__">
    <div class="card-header d-flex align-items-center justify-content-between py-2">
        <span class="fw-semibold level-label">Level __NUM__</span>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLevel(this)"><i class="bi bi-trash"></i></button>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Judul Level <span class="text-danger">*</span></label>
                <input type="text" name="levels[__IDX__][title]" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tipe Soal <span class="text-danger">*</span></label>
                <select name="levels[__IDX__][puzzle_type]" class="form-select puzzle-type-select" data-idx="__IDX__" onchange="onPuzzleTypeChange(this)" required>
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
            <textarea name="levels[__IDX__][instructions]" class="form-control" rows="2" required></textarea>
        </div>

        {{-- Dynamic fields container --}}
        <div class="puzzle-extra" data-idx="__IDX__">
            {{-- filled by JS --}}
        </div>

        {{-- Options & Answer --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Pilihan Jawaban <span class="text-danger">*</span> <small class="text-muted">(min 2)</small></label>
            <div class="options-container" data-idx="__IDX__">
                <div class="input-group mb-2">
                    <span class="input-group-text"><input type="radio" name="levels[__IDX__][answer_index]" value="0" required></span>
                    <input type="text" name="levels[__IDX__][options][]" class="form-control" placeholder="Pilihan A" required>
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text"><input type="radio" name="levels[__IDX__][answer_index]" value="1" required></span>
                    <input type="text" name="levels[__IDX__][options][]" class="form-control" placeholder="Pilihan B" required>
                </div>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addOption(this, __IDX__)"><i class="bi bi-plus me-1"></i>Tambah Pilihan</button>
        </div>
        <input type="hidden" name="levels[__IDX__][answer]" class="answer-hidden" value="">

        <div class="mb-3">
            <label class="form-label fw-semibold">Penjelasan (opsional)</label>
            <textarea name="levels[__IDX__][explanation]" class="form-control" rows="2"></textarea>
        </div>

        <div>
            <label class="form-label fw-semibold">Hints (opsional)</label>
            <div class="hints-container" data-idx="__IDX__">
                <div class="input-group mb-2">
                    <input type="text" name="levels[__IDX__][hints][]" class="form-control" placeholder="Hint 1">
                    <button type="button" class="btn btn-outline-secondary" onclick="addHint(this, __IDX__)"><i class="bi bi-plus"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

@push('scripts')
<script>
let levelCount = 0;

function addLevel() {
    const idx = levelCount++;
    const num = document.querySelectorAll('.level-card').length + 1;
    const tpl = document.getElementById('levelTemplate').innerHTML
        .replace(/__IDX__/g, idx)
        .replace(/__NUM__/g, num);
    const div = document.createElement('div');
    div.innerHTML = tpl;
    document.getElementById('levelsContainer').appendChild(div.firstElementChild);
    document.getElementById('emptyMsg').style.display = 'none';
    // init puzzle type extra fields
    const sel = document.querySelector(`[data-idx="${idx}"].puzzle-type-select`);
    if (sel) renderPuzzleExtra(idx, sel.value);
    renumberLevels();
}

function removeLevel(btn) {
    btn.closest('.level-card').remove();
    if (!document.querySelectorAll('.level-card').length)
        document.getElementById('emptyMsg').style.display = '';
    renumberLevels();
}

function renumberLevels() {
    document.querySelectorAll('.level-card').forEach((card, i) => {
        card.querySelector('.level-label').textContent = `Level ${i + 1}`;
    });
}

function onPuzzleTypeChange(sel) {
    const idx = sel.dataset.idx;
    renderPuzzleExtra(idx, sel.value);
}

function renderPuzzleExtra(idx, type) {
    const container = document.querySelector(`.puzzle-extra[data-idx="${idx}"]`);
    let html = '';
    if (type === 'multiple_choice') {
        html = `<div class="mb-3"><label class="form-label fw-semibold">Pertanyaan</label>
            <input type="text" name="levels[${idx}][question]" class="form-control"></div>`;
    } else if (type === 'code_output' || type === 'code_debug') {
        html = `<div class="mb-3"><label class="form-label fw-semibold">Kode</label>
            <textarea name="levels[${idx}][code]" class="form-control font-monospace" rows="5" placeholder="Tulis kode di sini..."></textarea></div>
            <div class="mb-3"><label class="form-label fw-semibold">Bahasa</label>
            <select name="levels[${idx}][language]" class="form-select">
                <option value="python">Python</option><option value="javascript">JavaScript</option>
                <option value="java">Java</option><option value="c">C</option><option value="cpp">C++</option>
            </select></div>`;
        if (type === 'code_debug') {
            html += `<div class="mb-3"><label class="form-label fw-semibold">Deskripsi Bug</label>
                <input type="text" name="levels[${idx}][bug]" class="form-control" placeholder="Apa bug yang harus diperbaiki?"></div>`;
        }
    } else if (type === 'riddle') {
        html = `<div class="mb-3"><label class="form-label fw-semibold">Teka-teki</label>
            <textarea name="levels[${idx}][riddle]" class="form-control" rows="2"></textarea></div>`;
    } else if (type === 'math') {
        html = `<div class="mb-3"><label class="form-label fw-semibold">Ekspresi Matematika</label>
            <input type="text" name="levels[${idx}][expression]" class="form-control" placeholder="Contoh: 2x + 5 = 13, x = ?"></div>`;
    } else if (type === 'number_sequence') {
        html = `<div class="mb-3"><label class="form-label fw-semibold">Urutan (pisahkan koma, kosongkan untuk blank)</label>
            <input type="text" name="levels[${idx}][sequence]" class="form-control" placeholder="1,2,3,,5,6">
            </div>
            <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label fw-semibold">Indeks Blank (mulai 0)</label>
            <input type="number" name="levels[${idx}][blank_index]" class="form-control" value="3" min="0"></div>
            <div class="col-6"><label class="form-label fw-semibold">Pola</label>
            <input type="text" name="levels[${idx}][pattern]" class="form-control" placeholder="Contoh: +1 setiap angka"></div>
            </div>`;
    } else if (type === 'anagram') {
        html = `<div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label fw-semibold">Kata Diacak</label>
            <input type="text" name="levels[${idx}][scrambled]" class="form-control" placeholder="TAIUNGBT"></div>
            <div class="col-6"><label class="form-label fw-semibold">Jawaban Kata</label>
            <input type="text" name="levels[${idx}][answer_word]" class="form-control" placeholder="BINTANG"></div>
            </div>`;
    }
    container.innerHTML = html;
}

function addOption(btn, idx) {
    const container = btn.previousElementSibling;
    const count = container.querySelectorAll('.input-group').length;
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<span class="input-group-text"><input type="radio" name="levels[${idx}][answer_index]" value="${count}" required></span>
        <input type="text" name="levels[${idx}][options][]" class="form-control" placeholder="Pilihan ${String.fromCharCode(65+count)}" required>
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-x"></i></button>`;
    container.appendChild(div);
}

function addHint(btn, idx) {
    const container = btn.closest('.hints-container');
    const count = container.querySelectorAll('.input-group').length + 1;
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<input type="text" name="levels[${idx}][hints][]" class="form-control" placeholder="Hint ${count}">
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-x"></i></button>`;
    container.appendChild(div);
}

// Start with 1 level
addLevel();
</script>
@endpush
@endsection

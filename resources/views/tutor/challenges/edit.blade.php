@extends('layouts.tutor')
@section('title', 'Kelola Challenge')
@section('page-title', 'Kelola Challenge')

@push('styles')
<style>
.nav-tabs .nav-link { color:#6c757d; font-weight:500; }
.nav-tabs .nav-link.active { color:#0d6efd; font-weight:600; }
.level-row { border-left:3px solid #dee2e6; transition:border-color .2s; }
.level-row:hover { border-left-color:#0d6efd; }
.level-num { width:36px;height:36px;border-radius:50%;background:#e9ecef;font-weight:700;font-size:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.puzzle-badge { font-size:11px;padding:2px 8px;border-radius:20px;background:#ede9fe;color:#5b21b6; }
.add-level-section { background:#f8f9fa; border:2px dashed #dee2e6; border-radius:12px; padding:24px; }
.code-preview { background:#1e1e1e; color:#d4d4d4; padding:12px; border-radius:6px; font-size:12px; max-height:120px; overflow:auto; white-space:pre; }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('tutor.courses.show', $course) }}" class="text-decoration-none">{{ Str::limit($course->title, 30) }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('tutor.challenges.index', $course) }}" class="text-decoration-none">Challenges</a></li>
        <li class="breadcrumb-item active">{{ Str::limit($challenge->title, 30) }}</li>
    </ol>
</nav>

{{-- Title Row --}}
<div class="d-flex align-items-start justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center rounded-3 bg-light" style="width:52px;height:52px;font-size:1.6rem;flex-shrink:0;">{{ $challenge->icon }}</div>
        <div>
            <h4 class="fw-bold mb-0">{{ $challenge->title }}</h4>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-{{ $challenge->difficulty_badge }}">{{ ucfirst($challenge->difficulty) }}</span>
                <span class="{{ $challenge->is_active ? 'badge bg-success' : 'badge bg-secondary' }}">{{ $challenge->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                <span class="text-muted small">{{ $challenge->levels->count() }} level · {{ $challenge->total_points }} poin · {{ $challenge->time_limit }} mnt</span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-info fs-6 px-3 py-2"><i class="bi bi-people me-1"></i>{{ $uniquePlayers }} pemain</span>
        <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-play me-1"></i>{{ $attemptsCount }}x dicoba</span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i><strong>Terdapat kesalahan:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="challengeTabs">
    <li class="nav-item">
        <a class="nav-link {{ !session('tab') || session('tab') === 'info' ? 'active' : '' }}" href="#info" data-bs-toggle="tab">
            <i class="bi bi-info-circle me-1"></i>Info Challenge
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ session('tab') === 'levels' ? 'active' : '' }}" href="#levels" data-bs-toggle="tab">
            <i class="bi bi-layers me-1"></i>Kelola Level
            <span class="badge bg-primary rounded-pill ms-1">{{ $challenge->levels->count() }}</span>
        </a>
    </li>
</ul>

<div class="tab-content">

    {{-- ── Tab: Info Challenge ──────────────────────────────────── --}}
    <div class="tab-pane fade {{ !session('tab') || session('tab') === 'info' ? 'show active' : '' }}" id="info">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header fw-semibold">Edit Info Challenge</div>
                    <div class="card-body">
                        <form action="{{ route('tutor.challenges.update', [$course, $challenge]) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $challenge->title) }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $challenge->description) }}</textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Kesulitan <span class="text-danger">*</span></label>
                                    <select name="difficulty" class="form-select" required>
                                        @foreach(['easy'=>'Mudah','medium'=>'Sedang','hard'=>'Sulit'] as $v=>$l)
                                        <option value="{{ $v }}" {{ old('difficulty',$challenge->difficulty)===$v?'selected':'' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Waktu (menit) <span class="text-danger">*</span></label>
                                    <input type="number" name="time_limit" class="form-control @error('time_limit') is-invalid @enderror"
                                           value="{{ old('time_limit', $challenge->time_limit) }}" min="1" max="180" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Poin/Level <span class="text-danger">*</span></label>
                                    <input type="number" name="points_per_level" class="form-control @error('points_per_level') is-invalid @enderror"
                                           value="{{ old('points_per_level', $challenge->points_per_level) }}" min="1" max="100" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Icon (emoji)</label>
                                <input type="text" name="icon" class="form-control" value="{{ old('icon', $challenge->icon) }}" maxlength="10">
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           {{ $challenge->is_active ? 'checked' : '' }} id="swActive">
                                    <label class="form-check-label fw-semibold" for="swActive">Challenge Aktif (terlihat oleh mahasiswa)</label>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                                <a href="{{ route('tutor.challenges.index', $course) }}" class="btn btn-outline-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-danger">
                    <div class="card-header fw-semibold text-danger bg-danger bg-opacity-10">
                        <i class="bi bi-exclamation-triangle me-2"></i>Zona Berbahaya
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Menghapus challenge akan menghapus semua level dan riwayat percobaan mahasiswa secara permanen.</p>
                        <button type="button" class="btn btn-danger btn-sm w-100"
                                onclick="confirmDeleteChallenge('{{ route('tutor.challenges.destroy', [$course, $challenge]) }}', '{{ addslashes($challenge->title) }}')">
                            <i class="bi bi-trash me-1"></i>Hapus Challenge Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tab: Kelola Level ────────────────────────────────────── --}}
    <div class="tab-pane fade {{ session('tab') === 'levels' ? 'show active' : '' }}" id="levels">

        {{-- Existing Levels --}}
        @if($challenge->levels->count())
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Daftar Level ({{ $challenge->levels->count() }})</span>
                <span class="text-muted small">Total: {{ $challenge->total_points }} poin</span>
            </div>
            <div class="list-group list-group-flush">
                @foreach($challenge->levels as $lvl)
                <div class="list-group-item level-row px-4 py-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="level-num">{{ $lvl->level_number }}</div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <span class="fw-semibold">{{ $lvl->title }}</span>
                                    <span class="puzzle-badge ms-2">{{ $lvl->puzzle_data['type'] ?? '-' }}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDeleteLevel('{{ route('tutor.challenges.levels.destroy', [$course, $challenge, $lvl]) }}', {{ $lvl->level_number }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <p class="text-muted small mb-1 mt-1">{{ Str::limit($lvl->instructions, 80) }}</p>

                            {{-- Preview puzzle data --}}
                            @php $pd = $lvl->puzzle_data; @endphp
                            @if(in_array($pd['type'] ?? '', ['code_output','code_debug']))
                            <div class="code-preview">{{ $pd['code'] ?? '' }}</div>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($pd['options'] ?? [] as $oi => $opt)
                                <span class="badge {{ $oi == ($pd['answer_index'] ?? -1) ? 'bg-success' : 'bg-light text-dark border' }}">
                                    {{ chr(65+$oi) }}. {{ Str::limit($opt, 30) }}
                                    @if($oi == ($pd['answer_index'] ?? -1)) <i class="bi bi-check-lg"></i>@endif
                                </span>
                                @endforeach
                            </div>
                            @if($lvl->hints && count($lvl->hints))
                            <div class="mt-1 text-muted small"><i class="bi bi-lightbulb me-1 text-warning"></i>{{ count($lvl->hints) }} hint tersedia</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Challenge ini belum memiliki level. Tambahkan level pertama di bawah.</div>
        @endif

        {{-- Add New Level Form --}}
        <div class="add-level-section">
            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Level Baru (Level {{ $challenge->levels->count() + 1 }})</h6>
            <form action="{{ route('tutor.challenges.levels.store', [$course, $challenge]) }}" method="POST" id="addLevelForm">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Judul Level <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Variabel & Tipe Data" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tipe Soal <span class="text-danger">*</span></label>
                        <select name="puzzle_type" class="form-select" id="newPuzzleType" onchange="renderNewPuzzleExtra()" required>
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
                    <textarea name="instructions" class="form-control" rows="2" placeholder="Tulis instruksi atau pertanyaan untuk level ini..." required></textarea>
                </div>

                {{-- Dynamic extra fields --}}
                <div id="newPuzzleExtra" class="mb-3"></div>

                {{-- Options --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilihan Jawaban <span class="text-danger">*</span></label>
                    <div id="newOptionsContainer">
                        <div class="input-group mb-2">
                            <span class="input-group-text"><input type="radio" name="answer_index" value="0" required></span>
                            <span class="input-group-text text-muted">A</span>
                            <input type="text" name="options[]" class="form-control" placeholder="Pilihan A" required>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><input type="radio" name="answer_index" value="1" required></span>
                            <span class="input-group-text text-muted">B</span>
                            <input type="text" name="options[]" class="form-control" placeholder="Pilihan B" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addNewOption()">
                        <i class="bi bi-plus me-1"></i>Tambah Pilihan
                    </button>
                    <small class="text-muted ms-2">Centang radio button untuk menandai jawaban benar.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Penjelasan Jawaban <small class="text-muted">(opsional)</small></label>
                    <textarea name="explanation" class="form-control" rows="2" placeholder="Jelaskan mengapa jawaban ini benar..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Hints <small class="text-muted">(opsional)</small></label>
                    <div id="newHintsContainer">
                        <div class="input-group mb-2">
                            <input type="text" name="hints[]" class="form-control" placeholder="Hint 1 (opsional)">
                            <button type="button" class="btn btn-outline-secondary" onclick="addNewHint()"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Level {{ $challenge->levels->count() + 1 }}
                </button>
            </form>
        </div>
    </div>

</div>{{-- /tab-content --}}

{{-- Delete forms --}}
<form id="deleteChallengeForm" method="POST" style="display:none">@csrf @method('DELETE')</form>
<form id="deleteLevelForm" method="POST" style="display:none">@csrf @method('DELETE')</form>

@endsection

@push('scripts')
<script>
// Auto-open levels tab if redirected after level action
@if(session('tab') === 'levels')
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Tab(document.querySelector('[href="#levels"]')).show();
});
@endif

// ── Delete helpers ────────────────────────────────────────────────────
function confirmDeleteChallenge(url, title) {
    if (confirm(`Hapus challenge "${title}"?\n\nSemua level dan riwayat percobaan siswa akan terhapus permanen!`)) {
        const f = document.getElementById('deleteChallengeForm');
        f.action = url;
        f.submit();
    }
}
function confirmDeleteLevel(url, num) {
    if (confirm(`Hapus Level ${num}?\n\nLevel yang tersisa akan diurutkan ulang secara otomatis.`)) {
        const f = document.getElementById('deleteLevelForm');
        f.action = url;
        f.submit();
    }
}

// ── Dynamic puzzle type extra fields ─────────────────────────────────
function renderNewPuzzleExtra() {
    const type = document.getElementById('newPuzzleType').value;
    const container = document.getElementById('newPuzzleExtra');
    let html = '';

    if (type === 'multiple_choice') {
        html = `<label class="form-label fw-semibold">Teks Pertanyaan</label>
            <input type="text" name="question" class="form-control" placeholder="Pertanyaan pilihan ganda...">`;
    } else if (type === 'code_output' || type === 'code_debug') {
        html = `<div class="mb-2"><label class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
            <textarea name="code" class="form-control font-monospace" rows="6" placeholder="Tulis kode di sini..." style="font-size:13px;"></textarea></div>
            <div class="mb-2"><label class="form-label fw-semibold">Bahasa</label>
            <select name="language" class="form-select">
                <option value="python">Python</option>
                <option value="javascript">JavaScript</option>
                <option value="php">PHP</option>
                <option value="java">Java</option>
                <option value="c">C</option>
                <option value="cpp">C++</option>
            </select></div>`;
        if (type === 'code_debug') {
            html += `<label class="form-label fw-semibold">Deskripsi Bug</label>
                <input type="text" name="bug" class="form-control" placeholder="Jelaskan bug yang harus ditemukan...">`;
        }
    } else if (type === 'riddle') {
        html = `<label class="form-label fw-semibold">Teks Teka-teki</label>
            <textarea name="riddle" class="form-control" rows="2" placeholder="Tulis teka-teki..."></textarea>`;
    } else if (type === 'math') {
        html = `<label class="form-label fw-semibold">Ekspresi Matematika</label>
            <input type="text" name="expression" class="form-control" placeholder="Contoh: (15 + 5) × 3 - 10 ÷ 2 = ?">`;
    } else if (type === 'number_sequence') {
        html = `<div class="mb-2"><label class="form-label fw-semibold">Urutan Angka <small class="text-muted">(pisahkan koma, kosongkan untuk blank)</small></label>
            <input type="text" name="sequence" class="form-control" placeholder="Contoh: 1,2,3,,5,6"></div>
            <div class="row g-2"><div class="col-6">
                <label class="form-label fw-semibold">Indeks Blank (mulai 0)</label>
                <input type="number" name="blank_index" class="form-control" value="3" min="0">
            </div><div class="col-6">
                <label class="form-label fw-semibold">Pola</label>
                <input type="text" name="pattern" class="form-control" placeholder="+2 setiap angka">
            </div></div>`;
    } else if (type === 'anagram') {
        html = `<div class="row g-2">
            <div class="col-6"><label class="form-label fw-semibold">Kata Diacak</label>
            <input type="text" name="scrambled" class="form-control" placeholder="TUTARO"></div>
            <div class="col-6"><label class="form-label fw-semibold">Jawaban Kata</label>
            <input type="text" name="answer_word" class="form-control" placeholder="TUTOR"></div>
            </div>`;
    }
    container.innerHTML = html;
}

// ── Options helper ────────────────────────────────────────────────────
function addNewOption() {
    const container = document.getElementById('newOptionsContainer');
    const count = container.querySelectorAll('.input-group').length;
    const letter = String.fromCharCode(65 + count);
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<span class="input-group-text"><input type="radio" name="answer_index" value="${count}" required></span>
        <span class="input-group-text text-muted">${letter}</span>
        <input type="text" name="options[]" class="form-control" placeholder="Pilihan ${letter}" required>
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove(); renumberOptions()">
            <i class="bi bi-x"></i>
        </button>`;
    container.appendChild(div);
}

function renumberOptions() {
    const rows = document.querySelectorAll('#newOptionsContainer .input-group');
    rows.forEach((row, i) => {
        row.querySelector('input[type=radio]').value = i;
        row.querySelector('.input-group-text.text-muted').textContent = String.fromCharCode(65 + i);
    });
}

function addNewHint() {
    const container = document.getElementById('newHintsContainer');
    const count = container.querySelectorAll('.input-group').length + 1;
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<input type="text" name="hints[]" class="form-control" placeholder="Hint ${count}">
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-x"></i></button>`;
    container.appendChild(div);
}

renderNewPuzzleExtra();
</script>
@endpush

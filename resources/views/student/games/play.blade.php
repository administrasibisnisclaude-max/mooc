@extends('layouts.app')

@section('title', 'Bermain - ' . $challenge->title)

@push('styles')
<style>
    body { background: #0f0c29; }
    .game-wrapper { min-height: calc(100vh - 60px); background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%); color: white; padding: 20px 0 60px; }
    .game-header { background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1); padding: 12px 0; }
    .timer-display { font-size: 1.8rem; font-weight: 800; font-family: monospace; }
    .timer-warning { color: #ff6b6b !important; animation: pulse 1s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.5} }
    .progress-bar-game { height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; }
    .progress-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #667eea, #a78bfa); transition: width 0.5s ease; }
    .level-card { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 20px; padding: 32px; max-width: 700px; margin: 0 auto; }
    .option-btn { background: rgba(255,255,255,0.08); border: 2px solid rgba(255,255,255,0.2); color: white; border-radius: 12px; padding: 14px 20px; text-align: left; transition: all 0.2s; width: 100%; cursor: pointer; font-size: 15px; }
    .option-btn:hover:not(:disabled) { background: rgba(102,126,234,0.3); border-color: #667eea; transform: translateX(4px); }
    .option-btn.correct { background: rgba(34,197,94,0.3) !important; border-color: #22c55e !important; }
    .option-btn.wrong { background: rgba(239,68,68,0.3) !important; border-color: #ef4444 !important; }
    .option-btn:disabled { cursor: default; transform: none !important; }
    .code-block { background: #1e1b4b; border-radius: 10px; padding: 16px; font-family: 'Courier New', monospace; font-size: 14px; color: #a5f3fc; white-space: pre-wrap; border-left: 4px solid #667eea; }
    .hint-box { background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.3); border-radius: 10px; padding: 14px; margin-top: 12px; }
    .result-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.85); display: flex; align-items: center; justify-content: center; z-index: 9999; }
    .result-card { background: linear-gradient(135deg, #1a1a3e, #2d2d5e); border-radius: 24px; padding: 48px; text-align: center; max-width: 500px; width: 90%; border: 1px solid rgba(255,255,255,0.1); }
    .score-circle { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #a78bfa); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 2rem; font-weight: 900; }
    .level-indicator { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; margin-bottom: 20px; }
    .level-pip { width: 28px; height: 28px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; transition: all 0.3s; }
    .level-pip.done { background: #22c55e; border-color: #22c55e; }
    .level-pip.current { background: #fbbf24; border-color: #fbbf24; color: #000; }
    .level-pip.wrong-pip { background: #ef4444; border-color: #ef4444; }
    .sequence-item { display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 12px; font-size: 1.2rem; font-weight: bold; margin: 4px; border: 2px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); }
    .sequence-blank { background: rgba(102,126,234,0.3); border-color: #667eea; border-style: dashed; color: #a5b4fc; }
    .feedback-banner { border-radius: 12px; padding: 16px 20px; margin-bottom: 16px; font-weight: 600; }
    .feedback-correct { background: rgba(34,197,94,0.2); border: 1px solid #22c55e; color: #86efac; }
    .feedback-wrong { background: rgba(239,68,68,0.2); border: 1px solid #ef4444; color: #fca5a5; }
    .emoji-option { font-size: 2rem; text-align: center; padding: 16px; }
</style>
@endpush

@section('content')
<div class="game-wrapper">
    <div class="game-header mb-4">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('student.games.show', $challenge) }}" class="btn btn-outline-light btn-sm" onclick="return confirmExit()">
                <i class="bi bi-x-lg me-1"></i>Keluar
            </a>
            <div class="text-center">
                <div class="timer-display" id="timer">{{ sprintf('%02d:%02d', $challenge->time_limit, 0) }}</div>
                <small class="opacity-60">Waktu Tersisa</small>
            </div>
            <div class="text-end">
                <div class="fw-bold fs-5" id="scoreDisplay">0</div>
                <small class="opacity-60">Poin</small>
            </div>
        </div>
        <div class="container mt-2">
            <div class="progress-bar-game">
                <div class="progress-fill" id="progressFill" style="width:0%"></div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="level-indicator mb-4" id="levelIndicator">
            @foreach($challenge->levels as $lvl)
            <div class="level-pip" id="pip-{{ $lvl->level_number }}" title="Level {{ $lvl->level_number }}">{{ $lvl->level_number }}</div>
            @endforeach
        </div>
        <div id="gameArea"></div>
    </div>
</div>

<div class="result-overlay d-none" id="resultOverlay">
    <div class="result-card">
        <div id="resultEmoji" class="display-2 mb-3">🏆</div>
        <h2 class="fw-bold mb-1" id="resultTitle">Selesai!</h2>
        <p class="text-muted mb-4" id="resultSubtitle"></p>
        <div class="score-circle mb-4"><span id="resultScore">0</span></div>
        <div class="row g-3 mb-4">
            <div class="col-4"><div class="bg-white bg-opacity-10 rounded-3 p-3"><div class="fw-bold" id="resultLevels">0/0</div><small class="opacity-75">Level</small></div></div>
            <div class="col-4"><div class="bg-white bg-opacity-10 rounded-3 p-3"><div class="fw-bold" id="resultCorrect">0</div><small class="opacity-75">Benar</small></div></div>
            <div class="col-4"><div class="bg-white bg-opacity-10 rounded-3 p-3"><div class="fw-bold" id="resultTime">0:00</div><small class="opacity-75">Waktu</small></div></div>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('student.games.leaderboard') }}" class="btn btn-warning btn-lg fw-bold">
                <i class="bi bi-trophy-fill me-2"></i>Lihat Leaderboard
            </a>
            <a href="{{ route('student.games.show', $challenge) }}" class="btn btn-outline-light">Main Lagi</a>
        </div>
    </div>
</div>

<script>
const LEVELS = @json($challenge->levels->sortBy('level_number')->values());
const CHECK_URL = '{{ route("student.games.check", $challenge) }}';
const SAVE_URL = '{{ route("student.games.save", $challenge) }}';
const CSRF = '{{ csrf_token() }}';
const PTS_PER_LEVEL = {{ $challenge->points_per_level }};
const TIME_LIMIT_SEC = {{ $challenge->time_limit * 60 }};

let currentLevel = 0, score = 0, correctCount = 0, answered = false;
let timerInterval = null, timeLeft = TIME_LIMIT_SEC, timeTaken = 0;

function startTimer() {
    timerInterval = setInterval(() => {
        timeLeft--; timeTaken++;
        updateTimerDisplay();
        if (timeLeft <= 0) { clearInterval(timerInterval); endGame(false); }
    }, 1000);
}

function updateTimerDisplay() {
    const m = Math.floor(timeLeft / 60), s = timeLeft % 60;
    const el = document.getElementById('timer');
    el.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    el.className = 'timer-display' + (timeLeft <= 60 ? ' timer-warning' : '');
}

function updateScore() {
    document.getElementById('scoreDisplay').textContent = score;
    document.getElementById('progressFill').style.width = (currentLevel / LEVELS.length * 100) + '%';
}

function updatePip(lvl, status) {
    const pip = document.getElementById('pip-' + lvl);
    if (!pip) return;
    pip.className = 'level-pip ' + status;
    if (status === 'done') pip.innerHTML = '✓';
    else if (status === 'wrong-pip') pip.innerHTML = '✗';
    else pip.textContent = lvl;
}

function renderLevel(levelData) {
    answered = false;
    updatePip(levelData.level_number, 'current');
    updateScore();
    const d = levelData.puzzle_data;
    let html = `<div class="level-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary rounded-pill px-3 py-2">Level ${levelData.level_number}/${LEVELS.length}</span>
            <span class="badge bg-warning text-dark rounded-pill">${PTS_PER_LEVEL} poin</span>
        </div>
        <h4 class="fw-bold mb-2">${escHtml(levelData.title)}</h4>
        <p class="opacity-80 mb-4">${escHtml(levelData.instructions)}</p>`;

    if (d.type === 'code_output' || d.type === 'code_debug') {
        html += `<div class="code-block mb-4">${escHtml(d.code)}</div>`;
        if (d.bug) html += `<p class="text-warning mb-3"><i class="bi bi-bug me-1"></i>Temukan bugnya!</p>`;
    }
    if (d.type === 'multiple_choice' && d.question) html += `<p class="fw-semibold mb-3 fs-5">${escHtml(d.question)}</p>`;
    if (d.type === 'number_sequence') {
        html += `<div class="text-center mb-4">`;
        d.sequence.forEach(n => { html += n === null ? `<span class="sequence-item sequence-blank">?</span>` : `<span class="sequence-item">${n}</span>`; });
        html += `</div>`;
    }
    if (d.type === 'visual_pattern') {
        html += `<div class="text-center mb-4" style="font-size:2.5rem;">`;
        d.sequence.forEach(s => { html += `<span class="me-2">${s}</span>`; });
        html += `</div>`;
    }
    if (d.type === 'anagram') html += `<div class="text-center mb-4"><div class="display-4 fw-bold" style="letter-spacing:12px;color:#a5b4fc;">${escHtml(d.scrambled)}</div><p class="text-muted mt-2">Susun ulang menjadi kata yang benar</p></div>`;
    if (d.type === 'riddle') html += `<div class="p-4 rounded-3 mb-4" style="background:rgba(167,139,250,0.1);border:1px solid rgba(167,139,250,0.3);"><p class="fs-5 mb-0 fst-italic">"${escHtml(d.riddle)}"</p></div>`;
    if (d.type === 'math') html += `<div class="text-center mb-4 p-4 rounded-3" style="background:rgba(255,255,255,0.05);"><span class="fs-2 fw-bold" style="color:#fbbf24;">${escHtml(d.expression)}</span></div>`;
    if (d.type === 'logic_grid') {
        html += `<div class="mb-4 p-3 rounded-3" style="background:rgba(255,255,255,0.05);">`;
        d.clues.forEach((c,i) => { html += `<div class="mb-1"><span class="badge bg-info me-2">${i+1}</span>${escHtml(c)}</div>`; });
        html += `</div>`;
    }

    html += `<div id="optionsArea" class="d-grid gap-2">`;
    d.options.forEach((opt, i) => {
        const isEmoji = /^\p{Emoji}/u.test(opt);
        html += `<button class="option-btn${isEmoji ? ' emoji-option' : ''}" onclick="selectAnswer(${i}, this)" data-idx="${i}">${isEmoji ? opt : escHtml(opt)}</button>`;
    });
    html += `</div><div id="feedbackArea" class="mt-3"></div>`;
    html += `<div class="d-flex justify-content-between align-items-center mt-4">
        <button class="btn btn-outline-warning btn-sm" onclick="showHint()" id="hintBtn"><i class="bi bi-lightbulb me-1"></i>Hint</button>
        <button class="btn btn-primary px-4" id="nextBtn" onclick="nextLevel()" style="display:none;">${currentLevel + 1 >= LEVELS.length ? 'Selesai 🎉' : 'Level Berikutnya →'}</button>
    </div><div id="hintArea"></div></div>`;

    document.getElementById('gameArea').innerHTML = html;
}

async function selectAnswer(idx, btn) {
    if (answered) return;
    answered = true;
    document.querySelectorAll('.option-btn').forEach(b => b.disabled = true);
    const level = LEVELS[currentLevel];
    try {
        const res = await fetch(CHECK_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({level_number: level.level_number, answer: String(idx), time_taken: TIME_LIMIT_SEC - timeLeft})
        });
        const data = await res.json();
        const feedbackEl = document.getElementById('feedbackArea');
        const correctIdx = level.puzzle_data.answer_index;
        if (data.correct) {
            btn.classList.add('correct'); score += PTS_PER_LEVEL; correctCount++;
            updatePip(level.level_number, 'done');
            feedbackEl.innerHTML = `<div class="feedback-banner feedback-correct">✅ Benar! ${data.explanation ? escHtml(data.explanation) : 'Jawaban tepat!'}</div>`;
        } else {
            btn.classList.add('wrong'); updatePip(level.level_number, 'wrong-pip');
            document.querySelectorAll('.option-btn')[correctIdx]?.classList.add('correct');
            feedbackEl.innerHTML = `<div class="feedback-banner feedback-wrong">❌ Kurang tepat. ${data.explanation ? escHtml(data.explanation) : 'Coba lagi nanti!'}</div>`;
        }
    } catch(e) {
        const correctIdx = level.puzzle_data.answer_index;
        if (idx === correctIdx) { btn.classList.add('correct'); score += PTS_PER_LEVEL; correctCount++; updatePip(level.level_number, 'done'); }
        else { btn.classList.add('wrong'); updatePip(level.level_number, 'wrong-pip'); document.querySelectorAll('.option-btn')[correctIdx]?.classList.add('correct'); }
    }
    updateScore();
    document.getElementById('nextBtn').style.display = 'inline-block';
}

function showHint() {
    const hints = LEVELS[currentLevel].hints || [];
    if (!hints.length) return;
    document.getElementById('hintArea').innerHTML = `<div class="hint-box"><i class="bi bi-lightbulb-fill text-warning me-2"></i>${hints.map(h=>escHtml(h)).join(' • ')}</div>`;
    document.getElementById('hintBtn').disabled = true;
}

function nextLevel() {
    currentLevel++;
    if (currentLevel >= LEVELS.length) endGame(true);
    else renderLevel(LEVELS[currentLevel]);
}

async function endGame(completed) {
    clearInterval(timerInterval);
    try {
        await fetch(SAVE_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({score, levels_completed: correctCount, time_taken: timeTaken, is_completed: completed && correctCount === LEVELS.length})
        });
    } catch(e) {}
    const overlay = document.getElementById('resultOverlay');
    overlay.classList.remove('d-none');
    const pct = Math.round(score / (LEVELS.length * PTS_PER_LEVEL) * 100);
    let emoji = '😢', title = 'Terus Berlatih!';
    if (pct >= 90) { emoji = '🏆'; title = 'Luar Biasa!'; }
    else if (pct >= 70) { emoji = '🎉'; title = 'Bagus Sekali!'; }
    else if (pct >= 50) { emoji = '👍'; title = 'Cukup Baik!'; }
    if (!completed) { emoji = '⏰'; title = 'Waktu Habis!'; }
    document.getElementById('resultEmoji').textContent = emoji;
    document.getElementById('resultTitle').textContent = title;
    document.getElementById('resultSubtitle').textContent = `Kamu meraih ${pct}% dari skor maksimal`;
    document.getElementById('resultScore').textContent = score;
    document.getElementById('resultLevels').textContent = `${correctCount}/${LEVELS.length}`;
    document.getElementById('resultCorrect').textContent = correctCount;
    const tm = TIME_LIMIT_SEC - timeLeft;
    document.getElementById('resultTime').textContent = `${Math.floor(tm/60)}:${String(tm%60).padStart(2,'0')}`;
}

function escHtml(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function confirmExit() { return confirm('Yakin ingin keluar? Progres tidak akan tersimpan.'); }

document.addEventListener('DOMContentLoaded', () => { startTimer(); renderLevel(LEVELS[0]); });
</script>
@endsection

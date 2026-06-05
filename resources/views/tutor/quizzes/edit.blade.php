@extends('layouts.tutor')
@section('title', 'Edit Quiz')
@section('page-title', 'Kelola Soal Quiz')

@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Tambah Soal</h6></div>
            <div class="card-body">
                <form action="{{ route('tutor.quizzes.add-question', [$course, $quiz]) }}" method="POST" id="questionForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pertanyaan</label>
                        <textarea name="question" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tipe</label>
                        <select name="type" class="form-select" id="qType" onchange="updateOptions()">
                            <option value="multiple_choice">Pilihan Ganda</option>
                            <option value="true_false">Benar/Salah</option>
                        </select>
                    </div>
                    <div id="optionsContainer">
                        <label class="form-label small fw-semibold">Pilihan Jawaban</label>
                        @for($i=0; $i<4; $i++)
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input type="radio" name="correct_option" value="{{ $i }}" {{ $i===0?'checked':'' }}>
                            </div>
                            <input type="text" name="options[]" class="form-control form-control-sm" placeholder="Opsi {{ $i+1 }}" {{ $i<2?'required':'' }}>
                        </div>
                        @endfor
                        <small class="text-muted">Pilih radio button untuk jawaban benar</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 mt-3">Tambah Soal</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="d-flex justify-content-between mb-3">
            <h6 class="fw-bold">{{ $quiz->title }} <span class="text-muted fw-normal small">({{ $quiz->questions->count() }} soal)</span></h6>
            <a href="{{ route('tutor.quizzes.index', $course) }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
        @forelse($quiz->questions as $i => $question)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <p class="fw-semibold mb-2"><span class="text-muted">{{ $i+1 }}.</span> {{ $question->question }}</p>
                    <form action="{{ route('tutor.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Hapus soal?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
                <div class="row g-1">
                    @foreach($question->options as $option)
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2 p-2 rounded {{ $option->is_correct ? 'bg-success bg-opacity-10 border border-success' : 'bg-light' }}">
                            @if($option->is_correct)<i class="bi bi-check-circle-fill text-success"></i>@else<i class="bi bi-circle text-muted"></i>@endif
                            <small>{{ $option->option_text }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="card text-center py-4 text-muted">Belum ada soal. Tambahkan di sebelah kiri.</div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function updateOptions() {
    const type = document.getElementById('qType').value;
    const container = document.getElementById('optionsContainer');
    if (type === 'true_false') {
        container.innerHTML = `<label class="form-label small fw-semibold">Pilihan</label>
            <div class="input-group mb-2"><div class="input-group-text"><input type="radio" name="correct_option" value="0" checked></div><input type="text" name="options[]" class="form-control form-control-sm" value="Benar" readonly></div>
            <div class="input-group mb-2"><div class="input-group-text"><input type="radio" name="correct_option" value="1"></div><input type="text" name="options[]" class="form-control form-control-sm" value="Salah" readonly></div>`;
    } else {
        container.innerHTML = `<label class="form-label small fw-semibold">Pilihan Jawaban</label>
            ${[0,1,2,3].map(i => `<div class="input-group mb-2"><div class="input-group-text"><input type="radio" name="correct_option" value="${i}" ${i===0?'checked':''}></div><input type="text" name="options[]" class="form-control form-control-sm" placeholder="Opsi ${i+1}" ${i<2?'required':''}></div>`).join('')}
            <small class="text-muted">Pilih radio button untuk jawaban benar</small>`;
    }
}
</script>
@endpush
@endsection

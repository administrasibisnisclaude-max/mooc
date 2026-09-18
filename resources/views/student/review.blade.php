@extends('layouts.app')
@section('title', 'Beri Ulasan - ' . $course->title)

@section('content')
<div class="container py-5" style="max-width:680px;">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('student.learn', $course) }}">{{ Str::limit($course->title, 35) }}</a></li>
            <li class="breadcrumb-item active">Beri Ulasan</li>
        </ol>
    </nav>

    {{-- Course info header --}}
    <div class="d-flex gap-3 align-items-center mb-4">
        @if($course->thumbnail)
        <img src="{{ asset('storage/'.$course->thumbnail) }}" class="rounded" style="width:72px;height:56px;object-fit:cover;" alt="">
        @else
        <div class="rounded bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:72px;height:56px;">
            <i class="bi bi-play-btn fs-4 text-primary"></i>
        </div>
        @endif
        <div>
            <h5 class="fw-bold mb-0">{{ $course->title }}</h5>
            <span class="text-muted small">oleh {{ $course->tutor->name }}</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-star-fill text-warning me-2"></i>
                {{ $existing ? 'Edit Ulasan Anda' : 'Beri Ulasan Kursus' }}
            </h5>
        </div>
        <div class="card-body p-4">

            @if($existing)
            <div class="alert alert-info border-0 py-2 mb-4 small">
                <i class="bi bi-info-circle me-1"></i>
                Anda sudah memberi ulasan. Perubahan di bawah akan menggantikan ulasan sebelumnya.
            </div>
            @endif

            <form action="{{ route('student.review.store', $course) }}" method="POST" id="reviewForm">
                @csrf

                {{-- Star rating picker --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold mb-3">Rating <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 align-items-center mb-2" id="starPicker">
                        @for($i = 1; $i <= 5; $i++)
                        <label for="star{{ $i }}" class="star-label" data-value="{{ $i }}"
                               style="cursor:pointer;font-size:2rem;line-height:1;color:#dee2e6;transition:color .1s;">
                            ★
                        </label>
                        <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}"
                               class="d-none" {{ ($existing && $existing->rating == $i) ? 'checked' : '' }}>
                        @endfor
                    </div>
                    <p id="ratingLabel" class="text-muted small mb-0">Pilih bintang untuk memberi rating</p>
                    @error('rating')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Written review --}}
                <div class="mb-4">
                    <label for="review" class="form-label fw-semibold">Ulasan <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea name="review" id="review" class="form-control" rows="5"
                              maxlength="1000"
                              placeholder="Ceritakan pengalaman belajar Anda, apa yang Anda suka, dan saran untuk kursus ini...">{{ old('review', $existing?->review) }}</textarea>
                    <div class="d-flex justify-content-end mt-1">
                        <small class="text-muted"><span id="charCount">{{ strlen($existing?->review ?? '') }}</span>/1000</small>
                    </div>
                    @error('review')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-warning fw-semibold px-4">
                        <i class="bi bi-star me-1"></i>{{ $existing ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                    </button>
                    <a href="{{ route('student.learn', $course) }}" class="btn btn-outline-secondary">Batal</a>
                    @if($existing)
                    <form action="{{ route('student.review.destroy', $course) }}" method="POST" class="ms-auto"
                          onsubmit="return confirm('Hapus ulasan Anda?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Hapus Ulasan
                        </button>
                    </form>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.star-label.active { color: #ffc107 !important; }
.star-label:hover  { color: #ffc107 !important; }
</style>

<script>
const LABELS = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus! ⭐'];
const stars  = document.querySelectorAll('.star-label');
const inputs = document.querySelectorAll('input[name=rating]');
const lbl    = document.getElementById('ratingLabel');

function setStars(val) {
    stars.forEach((s, i) => s.classList.toggle('active', i < val));
    lbl.textContent = val ? LABELS[val] : 'Pilih bintang untuk memberi rating';
}

// Init from existing value
const checked = document.querySelector('input[name=rating]:checked');
if (checked) setStars(+checked.value);

stars.forEach((star, idx) => {
    star.addEventListener('mouseenter', () => setStars(idx + 1));
    star.addEventListener('mouseleave', () => {
        const c = document.querySelector('input[name=rating]:checked');
        setStars(c ? +c.value : 0);
    });
    star.addEventListener('click', () => {
        inputs[idx].checked = true;
        setStars(idx + 1);
    });
});

// Char counter
const textarea = document.getElementById('review');
const counter  = document.getElementById('charCount');
textarea.addEventListener('input', () => {
    counter.textContent = textarea.value.length;
});
</script>
@endsection

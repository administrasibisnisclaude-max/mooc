@extends('layouts.tutor')
@section('title', 'Edit Challenge')
@section('page-title', 'Edit Challenge')

@section('content')
<div class="mb-4">
    <a href="{{ route('tutor.challenges.index', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali ke Challenges</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('tutor.challenges.update', [$course, $challenge]) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $challenge->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $challenge->description) }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kesulitan</label>
                    <select name="difficulty" class="form-select" required>
                        @foreach(['easy'=>'Mudah','medium'=>'Sedang','hard'=>'Sulit'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('difficulty',$challenge->difficulty)===$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Batas Waktu (menit)</label>
                    <input type="number" name="time_limit" class="form-control" value="{{ old('time_limit', $challenge->time_limit) }}" min="1" max="180" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Poin/Level</label>
                    <input type="number" name="points_per_level" class="form-control" value="{{ old('points_per_level', $challenge->points_per_level) }}" min="1" max="100" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Icon (emoji)</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $challenge->icon) }}" maxlength="10" placeholder="🎯">
            </div>
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ $challenge->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Challenge Aktif</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('tutor.challenges.index', $course) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@if($challenge->levels->count())
<div class="mt-5">
    <h5 class="fw-bold mb-3">Level Challenge ({{ $challenge->levels->count() }})</h5>
    <div class="row g-3">
        @foreach($challenge->levels as $level)
        <div class="col-md-6">
            <div class="card border-start border-primary border-3">
                <div class="card-body py-2 px-3">
                    <div class="fw-semibold">Level {{ $level->level_number }}: {{ $level->title }}</div>
                    <div class="text-muted small">{{ $level->puzzle_data['type'] ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <p class="text-muted small mt-2"><i class="bi bi-info-circle me-1"></i>Level tidak dapat diedit setelah dibuat. Hapus challenge dan buat ulang untuk mengubah soal.</p>
</div>
@endif
@endsection

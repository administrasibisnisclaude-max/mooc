@extends('layouts.tutor')
@section('title', 'Tambah Materi')
@section('page-title', 'Tambah Materi')

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header bg-white">
        <p class="text-muted small mb-0">Bagian: <strong>{{ $section->title }}</strong></p>
    </div>
    <div class="card-body">
        <form action="{{ route('tutor.lessons.store', $section) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Materi <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tipe Materi</label>
                <select name="type" class="form-select" id="lessonType" onchange="toggleFields()">
                    <option value="video">Video</option>
                    <option value="document">Dokumen</option>
                    <option value="text">Teks</option>
                </select>
            </div>
            <div id="videoFields" class="mb-3">
                <label class="form-label fw-semibold">URL Video</label>
                <input type="text" name="video_url" class="form-control" value="{{ old('video_url') }}" placeholder="https://youtube.com/watch?v=...">
                <small class="text-muted">Gunakan link YouTube atau URL video langsung</small>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Konten / Deskripsi</label>
                <textarea name="content" class="form-control" rows="6" placeholder="Tuliskan deskripsi atau konten materi...">{{ old('content') }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Durasi (menit)</label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration', 0) }}" min="0">
                    <small class="text-muted">0 jika tidak ada durasi</small>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="is_free_preview" id="is_free_preview" value="1">
                        <label class="form-check-label fw-semibold" for="is_free_preview">Preview Gratis</label>
                        <div class="text-muted small">Non-peserta bisa akses</div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Materi</button>
                <a href="{{ route('tutor.courses.show', $section->course_id) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleFields() {
    const type = document.getElementById('lessonType').value;
    document.getElementById('videoFields').style.display = type === 'video' ? 'block' : 'none';
}
toggleFields();
</script>
@endpush
@endsection

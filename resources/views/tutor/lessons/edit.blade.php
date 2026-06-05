@extends('layouts.tutor')
@section('title', 'Edit Materi')
@section('page-title', 'Edit Materi')

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-body">
        <form action="{{ route('tutor.lessons.update', $lesson) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Materi</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $lesson->title) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tipe</label>
                <select name="type" class="form-select" id="lessonType" onchange="toggleFields()">
                    <option value="video" {{ $lesson->type === 'video' ? 'selected' : '' }}>Video</option>
                    <option value="document" {{ $lesson->type === 'document' ? 'selected' : '' }}>Dokumen</option>
                    <option value="text" {{ $lesson->type === 'text' ? 'selected' : '' }}>Teks</option>
                </select>
            </div>
            <div id="videoFields" class="mb-3">
                <label class="form-label fw-semibold">URL Video</label>
                <input type="text" name="video_url" class="form-control" value="{{ old('video_url', $lesson->video_url) }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Konten</label>
                <textarea name="content" class="form-control" rows="6">{{ old('content', $lesson->content) }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Durasi (menit)</label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration', intdiv($lesson->duration, 60)) }}" min="0">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="is_free_preview" id="is_free_preview" value="1" {{ $lesson->is_free_preview ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_free_preview">Preview Gratis</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('tutor.courses.show', $lesson->section->course_id) }}" class="btn btn-outline-secondary">Batal</a>
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

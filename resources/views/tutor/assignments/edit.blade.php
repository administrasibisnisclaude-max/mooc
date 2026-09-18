@extends('layouts.tutor')
@section('title', 'Edit Tugas')
@section('page-title', 'Edit Tugas')

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-body">
        <form action="{{ route('tutor.assignments.update', [$course, $assignment]) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Tugas</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $assignment->title) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" class="form-control" rows="5" required>{{ old('description', $assignment->description) }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Deadline</label>
                    <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nilai Maksimum</label>
                    <input type="number" name="max_score" class="form-control" value="{{ old('max_score', $assignment->max_score) }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ketentuan File</label>
                <textarea name="file_requirements" class="form-control" rows="2">{{ old('file_requirements', $assignment->file_requirements) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('tutor.assignments.index', $course) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

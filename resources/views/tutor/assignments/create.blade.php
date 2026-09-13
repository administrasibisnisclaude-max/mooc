@extends('layouts.tutor')
@section('title', 'Buat Tugas')
@section('page-title', 'Buat Tugas')

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-body">
        <form action="{{ route('tutor.assignments.store', $course) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Tugas</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Deadline (opsional)</label>
                    <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nilai Maksimum</label>
                    <input type="number" name="max_score" class="form-control" value="{{ old('max_score', 100) }}" min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bagian (opsional)</label>
                    <select name="section_id" class="form-select">
                        <option value="">-- Pilih Bagian --</option>
                        @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ketentuan File (opsional)</label>
                <textarea name="file_requirements" class="form-control" rows="2" placeholder="Contoh: PDF, maks 10MB">{{ old('file_requirements') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Buat Tugas</button>
                <a href="{{ route('tutor.assignments.index', $course) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.tutor')
@section('title', 'Buat Quiz')
@section('page-title', 'Buat Quiz')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('tutor.quizzes.store', $course) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Quiz</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Passing Score (%)</label>
                    <input type="number" name="passing_score" class="form-control" value="{{ old('passing_score', 70) }}" min="1" max="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Batas Waktu (menit, opsional)</label>
                    <input type="number" name="time_limit" class="form-control" value="{{ old('time_limit') }}" min="1">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Bagian (opsional)</label>
                <select name="section_id" class="form-select">
                    <option value="">-- Tidak terikat bagian --</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Buat Quiz & Tambah Soal</button>
                <a href="{{ route('tutor.quizzes.index', $course) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.tutor')
@section('title', 'Edit Kursus')
@section('page-title', 'Edit Kursus')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('tutor.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Judul Kursus</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $course->title) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $course->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="5" required>{{ old('description', $course->description) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Level</label>
                    <select name="level" class="form-select">
                        <option value="beginner" {{ $course->level === 'beginner' ? 'selected' : '' }}>Pemula</option>
                        <option value="intermediate" {{ $course->level === 'intermediate' ? 'selected' : '' }}>Menengah</option>
                        <option value="advanced" {{ $course->level === 'advanced' ? 'selected' : '' }}>Mahir</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Harga (Rp)</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $course->price) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bahasa</label>
                    <input type="text" name="language" class="form-control" value="{{ old('language', $course->language) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Yang Akan Dipelajari</label>
                    <textarea name="what_youll_learn" class="form-control" rows="4">{{ old('what_youll_learn', $course->what_youll_learn) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Persyaratan</label>
                    <textarea name="requirements" class="form-control" rows="4">{{ old('requirements', $course->requirements) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Thumbnail Baru (opsional)</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" class="mt-2 rounded" style="height:80px;" alt="">
                    @endif
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Perbarui Kursus</button>
                    <a href="{{ route('tutor.courses.show', $course) }}" class="btn btn-outline-secondary ms-2">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

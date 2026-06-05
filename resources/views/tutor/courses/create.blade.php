@extends('layouts.tutor')
@section('title', 'Buat Kursus')
@section('page-title', 'Buat Kursus Baru')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('tutor.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Judul Kursus <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Contoh: Belajar Laravel dari Nol" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi Kursus <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Level <span class="text-danger">*</span></label>
                    <select name="level" class="form-select">
                        <option value="beginner" {{ old('level') === 'beginner' ? 'selected' : '' }}>Pemula</option>
                        <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Menengah</option>
                        <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Mahir</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', 0) }}" min="0" step="1000">
                    <small class="text-muted">0 = Gratis</small>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bahasa</label>
                    <input type="text" name="language" class="form-control" value="{{ old('language', 'Indonesia') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Yang Akan Dipelajari</label>
                    <textarea name="what_youll_learn" class="form-control" rows="4" placeholder="Tulis satu poin per baris...">{{ old('what_youll_learn') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Persyaratan</label>
                    <textarea name="requirements" class="form-control" rows="4" placeholder="Tulis satu persyaratan per baris...">{{ old('requirements') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Thumbnail</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    <small class="text-muted">Disarankan: 1280x720px, max 2MB</small>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Buat Kursus</button>
                    <a href="{{ route('tutor.courses.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

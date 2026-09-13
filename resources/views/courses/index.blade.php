@extends('layouts.app')
@section('title', 'Semua Kursus')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <h2 class="fw-bold mb-1">Semua Kursus</h2>
        <p class="mb-0 opacity-75">Temukan kursus yang sesuai untuk Anda</p>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <!-- Filter Sidebar -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Filter Kursus</h6>
                    <form action="{{ route('courses.index') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Cari</label>
                            <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Judul kursus...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kategori</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Level</label>
                            <select name="level" class="form-select form-select-sm">
                                <option value="">Semua Level</option>
                                <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Pemula</option>
                                <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Menengah</option>
                                <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Mahir</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan Filter</button>
                        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">Reset</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Course Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">{{ $courses->total() }} kursus ditemukan</span>
            </div>
            <div class="row g-4">
                @forelse($courses as $course)
                <div class="col-md-6 col-lg-4">
                    @include('partials.course-card', ['course' => $course])
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Tidak ada kursus yang ditemukan.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary">Lihat Semua Kursus</a>
                </div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $courses->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

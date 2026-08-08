@extends('layouts.app')
@section('title', 'EduPlatform - Belajar Online Terbaik')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Belajar Tanpa Batas, <br>Raih Impianmu!</h1>
                <p class="lead mb-4 opacity-75">Platform e-learning terdepan dengan ribuan kursus berkualitas dari instruktur terbaik Indonesia.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('courses.index') }}" class="btn btn-warning btn-lg px-4 fw-semibold">Jelajahi Kursus</a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">Daftar Gratis</a>
                    @endguest
                </div>
                <div class="row mt-4 g-3">
                    <div class="col-auto">
                        <div class="text-center">
                            <h3 class="fw-bold mb-0">{{ number_format($totalCourses) }}+</h3>
                            <small class="opacity-75">Kursus</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-center">
                            <h3 class="fw-bold mb-0">{{ number_format($totalStudents) }}+</h3>
                            <small class="opacity-75">Pelajar</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-center">
                            <h3 class="fw-bold mb-0">100%</h3>
                            <small class="opacity-75">Online</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <div style="font-size: 200px; opacity: 0.2;">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold text-center mb-2">Jelajahi Kategori</h2>
        <p class="text-muted text-center mb-4">Temukan kursus yang sesuai minat dan kebutuhanmu</p>
        <div class="row g-3">
            @foreach($categories as $cat)
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('courses.index', ['category' => $cat->id]) }}" class="text-decoration-none">
                    <div class="category-card card text-center p-3 h-100 border-0 shadow-sm" style="transition:all 0.3s;cursor:pointer;">
                        <div class="fs-2 text-primary mb-2">{{ $cat->icon ?: '📚' }}</div>
                        <p class="fw-semibold mb-1 small">{{ $cat->name }}</p>
                        <small class="text-muted">{{ $cat->courses_count }} kursus</small>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Kursus Terbaru</h2>
                <p class="text-muted mb-0">Kursus pilihan dari instruktur terbaik</p>
            </div>
            <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="row g-4">
            @forelse($featuredCourses as $course)
            <div class="col-md-6 col-lg-3">
                @include('partials.course-card', ['course' => $course])
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada kursus tersedia.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Why EduPlatform -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold text-center mb-2">Mengapa EduPlatform?</h2>
        <p class="text-muted text-center mb-5">Kami menyediakan pengalaman belajar terbaik</p>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="p-4">
                    <div class="text-primary fs-1 mb-3"><i class="bi bi-play-circle-fill"></i></div>
                    <h5 class="fw-bold">Video Berkualitas</h5>
                    <p class="text-muted small">Belajar dengan video HD dari instruktur berpengalaman</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <div class="text-success fs-1 mb-3"><i class="bi bi-award-fill"></i></div>
                    <h5 class="fw-bold">Sertifikat Resmi</h5>
                    <p class="text-muted small">Dapatkan sertifikat setelah menyelesaikan kursus</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <div class="text-warning fs-1 mb-3"><i class="bi bi-chat-dots-fill"></i></div>
                    <h5 class="fw-bold">Forum Diskusi</h5>
                    <p class="text-muted small">Diskusi langsung dengan tutor dan sesama pelajar</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <div class="text-danger fs-1 mb-3"><i class="bi bi-phone-fill"></i></div>
                    <h5 class="fw-bold">Akses Kapanpun</h5>
                    <p class="text-muted small">Belajar dari mana saja dan kapan saja</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@guest
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Mulai Perjalanan Belajarmu Hari Ini!</h2>
        <p class="mb-4 opacity-75">Bergabung dengan ribuan pelajar yang telah meningkatkan keahlian mereka</p>
        <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5 fw-semibold">Daftar Gratis Sekarang</a>
    </div>
</section>
@endguest
@endsection

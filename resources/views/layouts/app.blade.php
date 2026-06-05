<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EduPlatform - Belajar Online Terbaik')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #0056D2;
            --primary-dark: #003a8c;
            --accent: #FF6B35;
        }
        body { font-family: 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: 800; color: var(--primary) !important; font-size: 1.5rem; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background: var(--primary) !important; }
        .course-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important; transition: all 0.3s; }
        .course-card { transition: all 0.3s; }
        .hero-section { background: linear-gradient(135deg, #0056D2 0%, #003a8c 100%); }
        .category-card:hover { background: var(--primary); color: white !important; }
        .category-card:hover .text-primary { color: white !important; }
        .footer { background: #1a1a2e; color: #ccc; }
        .star-rating .bi-star-fill { color: #ffc107; }
        .progress-bar { background: var(--primary); }
        .sidebar-link { color: #555; text-decoration: none; padding: 10px 15px; border-radius: 8px; display: block; margin-bottom: 4px; }
        .sidebar-link:hover, .sidebar-link.active { background: var(--primary); color: white; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top no-print">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-mortarboard-fill text-primary"></i> EduPlatform
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('courses.index') }}">Kursus</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                    @else
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:35px;height:35px;font-size:14px;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                @if(auth()->user()->isStudent())
                                    <li><a class="dropdown-item" href="{{ route('student.courses') }}"><i class="bi bi-book me-2"></i>Kursus Saya</a></li>
                                    <li><a class="dropdown-item" href="{{ route('student.certificates') }}"><i class="bi bi-award me-2"></i>Sertifikat</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0 rounded-0 no-print" role="alert">
            <div class="container">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0 rounded-0 no-print" role="alert">
            <div class="container">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')

    <footer class="footer py-5 mt-5 no-print">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="text-white fw-bold"><i class="bi bi-mortarboard-fill"></i> EduPlatform</h5>
                    <p class="small">Platform e-learning terbaik untuk mengembangkan keahlian Anda.</p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-white">Kursus</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('courses.index') }}" class="text-decoration-none text-secondary">Semua Kursus</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="text-white">Bergabung</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('register') }}" class="text-decoration-none text-secondary">Daftar</a></li>
                        <li><a href="{{ route('login') }}" class="text-decoration-none text-secondary">Masuk</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <p class="text-center small mb-0">&copy; {{ date('Y') }} EduPlatform. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

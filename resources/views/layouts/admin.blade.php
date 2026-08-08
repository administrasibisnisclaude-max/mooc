<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - EduPlatform</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary: #0056D2; }
        body { background: #f0f2f5; }
        .sidebar { width: 260px; min-height: 100vh; background: #1a1a2e; position: fixed; top: 0; left: 0; z-index: 100; overflow-y: auto; }
        .sidebar-brand { color: white; font-weight: 800; font-size: 1.3rem; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-section { color: rgba(255,255,255,0.4); font-size: 0.7rem; text-transform: uppercase; padding: 15px 20px 5px; letter-spacing: 1px; }
        .sidebar a { color: rgba(255,255,255,0.75); text-decoration: none; padding: 10px 20px; display: flex; align-items: center; gap: 10px; border-radius: 0; transition: all 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { border-left: 3px solid var(--primary); }
        .main-content { margin-left: 260px; padding: 25px; min-height: 100vh; }
        .topbar { background: white; padding: 15px 25px; margin: -25px -25px 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 12px; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .badge-status-pending { background: #fff3cd; color: #856404; }
        .badge-status-approved { background: #d1e7dd; color: #0f5132; }
        .badge-status-rejected { background: #f8d7da; color: #842029; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="bi bi-mortarboard-fill"></i> EduPlatform</div>
        <div class="sidebar-section">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <div class="sidebar-section">Manajemen</div>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Kelola User</a>
        <a href="{{ route('admin.tutors.index') }}" class="{{ request()->routeIs('admin.tutors.*') ? 'active' : '' }}"><i class="bi bi-person-check"></i> Verifikasi Tutor</a>
        <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"><i class="bi bi-book"></i> Verifikasi Course</a>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-grid"></i> Kategori</a>
        <div class="sidebar-section">Laporan</div>
        <a href="{{ route('admin.certificates.index') }}" class="{{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}"><i class="bi bi-award"></i> Sertifikat</a>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i> Laporan</a>
        <div class="sidebar-section">Akun</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="background:none;border:none;width:100%;text-align:left;color:rgba(255,255,255,0.75);padding:10px 20px;display:flex;align-items:center;gap:10px;cursor:pointer;">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </button>
        </form>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h6 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h6>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <span class="badge bg-danger">Admin</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

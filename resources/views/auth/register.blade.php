@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <h2 class="fw-bold text-primary"><i class="bi bi-mortarboard-fill"></i> EduPlatform</h2>
                    </a>
                    <p class="text-muted">Buat akun baru dan mulai belajar</p>
                </div>
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Nama lengkap Anda" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="nama@email.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Minimal 8 karakter" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Daftar Sebagai</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="role" id="role-student" value="student" checked>
                                        <label class="btn btn-outline-primary w-100" for="role-student">
                                            <i class="bi bi-person-fill d-block fs-4 mb-1"></i>
                                            <strong>Pelajar</strong>
                                            <br><small>Akses semua kursus</small>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="role" id="role-tutor" value="tutor">
                                        <label class="btn btn-outline-success w-100" for="role-tutor">
                                            <i class="bi bi-person-video d-block fs-4 mb-1"></i>
                                            <strong>Tutor</strong>
                                            <br><small>Buat & jual kursus</small>
                                        </label>
                                    </div>
                                </div>
                                @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Buat Akun</button>
                        </form>
                        <hr>
                        <p class="text-center mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary fw-semibold">Masuk</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

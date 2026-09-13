@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <h2 class="fw-bold text-primary"><i class="bi bi-mortarboard-fill"></i> EduPlatform</h2>
                    </a>
                    <p class="text-muted">Masuk ke akun Anda</p>
                </div>
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="nama@email.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
                        </form>
                        <hr>
                        <p class="text-center mb-0">Belum punya akun? <a href="{{ route('register') }}" class="text-primary fw-semibold">Daftar sekarang</a></p>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">Demo: admin@demo.com / password | tutor@demo.com / password | student@demo.com / password</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Sertifikat')

@section('content')
<div class="container py-4 text-center no-print">
    <button onclick="window.print()" class="btn btn-primary mb-3"><i class="bi bi-printer me-1"></i>Print / Download PDF</button>
    <a href="{{ route('student.certificates') }}" class="btn btn-outline-secondary mb-3 ms-2"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div id="certificate" style="max-width:800px;margin:0 auto;padding:20px;">
    <div class="border border-4 border-warning p-5 text-center" style="background: linear-gradient(135deg, #fff8e1 0%, #ffffff 50%, #fff8e1 100%);">
        <div class="text-primary fs-2 fw-bold mb-2"><i class="bi bi-mortarboard-fill"></i> EduPlatform</div>
        <p class="text-muted small mb-4">Platform E-Learning Terpercaya</p>

        <h2 class="text-muted fw-normal mb-1" style="font-size:1.2rem;letter-spacing:3px;text-transform:uppercase;">SERTIFIKAT KELULUSAN</h2>
        <div class="border-bottom border-warning my-3"></div>

        <p class="text-muted mb-1">Dengan bangga diberikan kepada</p>
        <h1 class="fw-bold mb-1" style="font-size:2.5rem;color:#0056D2;">{{ $certificate->user->name }}</h1>
        <p class="text-muted mb-3">atas keberhasilan menyelesaikan kursus</p>

        <div class="bg-primary text-white py-2 px-4 d-inline-block rounded mb-4">
            <h3 class="fw-bold mb-0">{{ $certificate->course->title }}</h3>
        </div>

        <p class="text-muted mb-1">Disampaikan oleh <strong>{{ $certificate->course->tutor->name }}</strong></p>

        <div class="row mt-4 g-3 justify-content-center">
            <div class="col-auto text-center">
                <div class="border-bottom border-dark px-5 mb-1" style="height:40px;display:flex;align-items:center;justify-content:center;">
                    <em style="font-size:1.3rem;color:#0056D2;">EduPlatform</em>
                </div>
                <small class="text-muted">Direktur</small>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top">
            <small class="text-muted">No. Sertifikat: <strong>{{ $certificate->certificate_number }}</strong></small>
            <br>
            <small class="text-muted">Diterbitkan: {{ $certificate->issued_at?->format('d F Y') }}</small>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    nav.navbar, footer { display: none !important; }
    #certificate { max-width: 100%; margin: 0; }
}
</style>
@endpush
@endsection

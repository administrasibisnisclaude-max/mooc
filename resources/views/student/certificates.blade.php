@extends('layouts.app')
@section('title', 'Sertifikat Saya')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <h4 class="fw-bold mb-0"><i class="bi bi-award me-2"></i>Sertifikat Saya</h4>
    </div>
</div>

<div class="container py-4">
    @if($certificates->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-award fs-1 text-muted"></i>
        <p class="text-muted mt-2">Belum ada sertifikat. Selesaikan kursus untuk mendapatkan sertifikat!</p>
        <a href="{{ route('student.courses') }}" class="btn btn-primary">Lihat Kursus Saya</a>
    </div>
    @else
    <div class="row g-4">
        @foreach($certificates as $cert)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="text-warning mb-3"><i class="bi bi-award-fill" style="font-size:4rem;"></i></div>
                    <h6 class="fw-bold mb-1">{{ $cert->course->title }}</h6>
                    <p class="text-muted small mb-3">Diterbitkan: {{ $cert->issued_at?->format('d M Y') }}</p>
                    <p class="text-muted small mb-3"><code>{{ $cert->certificate_number }}</code></p>
                    <a href="{{ route('student.certificate.show', $cert) }}" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-eye me-1"></i>Lihat Sertifikat
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

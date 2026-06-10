@extends('layouts.app')
@section('title', 'Checkout - ' . $course->title)

@section('content')
<div class="container py-5" style="max-width:960px;">
    <div class="row g-4">

        {{-- ── LEFT: Order Summary ──────────────────────────────── --}}
        <div class="col-lg-7">
            <h4 class="fw-bold mb-4">Ringkasan Pesanan</h4>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex gap-3">
                        @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}"
                             class="rounded" style="width:90px;height:70px;object-fit:cover;" alt="">
                        @else
                        <div class="rounded bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width:90px;height:70px;min-width:90px;">
                            <i class="bi bi-play-btn fs-3 text-primary"></i>
                        </div>
                        @endif
                        <div>
                            <h6 class="fw-bold mb-1">{{ $course->title }}</h6>
                            <p class="text-muted small mb-1">Oleh {{ $course->tutor->name }}</p>
                            <span class="badge bg-light text-dark border">{{ $course->category->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Yang Akan Anda Dapatkan</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Akses penuh ke semua materi</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Video pembelajaran berkualitas HD</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Quiz & tugas interaktif</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Forum diskusi dengan sesama peserta</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Sertifikat penyelesaian kursus</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Akses seumur hidup</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Payment Panel ─────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top:80px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Detail Pembayaran</h5>

                    @if(session('error'))
                    <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
                    @endif

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Harga Kursus</span>
                        <span>{{ $course->formatted_price }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold fs-5 text-primary">{{ $course->formatted_price }}</span>
                    </div>

                    {{-- If there's a pending order, show resume link --}}
                    @if($pendingOrder && $pendingOrder->xendit_invoice_url)
                    <a href="{{ $pendingOrder->xendit_invoice_url }}"
                       class="btn btn-warning w-100 py-2 mb-3 fw-semibold">
                        <i class="bi bi-arrow-right-circle me-2"></i>Lanjutkan Pembayaran
                    </a>
                    <p class="text-muted text-center" style="font-size:12px;">
                        Invoice sebelumnya masih aktif hingga {{ $pendingOrder->expires_at?->format('d M Y H:i') }}
                    </p>
                    <hr>
                    <p class="text-center text-muted small mb-2">atau buat invoice baru</p>
                    @endif

                    <form action="{{ route('checkout.pay', $course) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-6">
                            <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
                        </button>
                    </form>

                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="bi bi-shield-lock me-1"></i>Pembayaran aman via
                            <strong>Xendit</strong>
                        </small>
                    </div>

                    {{-- Payment methods icons --}}
                    <div class="mt-3 d-flex flex-wrap gap-2 justify-content-center">
                        @foreach(['bi-bank','bi-wallet2','bi-phone','bi-credit-card-2-front'] as $icon)
                        <span class="badge bg-light text-muted border px-2 py-2">
                            <i class="bi {{ $icon }} me-1"></i>
                        </span>
                        @endforeach
                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size:11px;">Transfer Bank</span>
                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size:11px;">e-Wallet</span>
                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size:11px;">QRIS</span>
                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size:11px;">Kartu Kredit</span>
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <a href="{{ route('courses.detail', $course->slug) }}" class="text-muted small">
                            <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail Kursus
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

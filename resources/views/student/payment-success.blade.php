@extends('layouts.app')
@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="container py-5" style="max-width:560px;">
    <div class="card border-0 shadow-sm text-center">
        <div class="card-body p-5">

            @if($order->isPaid())
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle"
                     style="width:90px;height:90px;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:2.8rem;"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-2">Pembayaran Berhasil!</h3>
            <p class="text-muted mb-4">
                Selamat! Anda kini terdaftar di kursus <strong>{{ $order->course->title }}</strong>.
                Mulai belajar sekarang.
            </p>

            <div class="bg-light rounded p-3 mb-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">No. Order</span>
                    <span class="small fw-semibold">{{ $order->order_number }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Kursus</span>
                    <span class="small fw-semibold">{{ Str::limit($order->course->title, 30) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total Dibayar</span>
                    <span class="small fw-semibold text-success">{{ $order->formatted_amount }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Tanggal</span>
                    <span class="small fw-semibold">{{ $order->paid_at?->format('d M Y H:i') ?? now()->format('d M Y H:i') }}</span>
                </div>
            </div>

            <a href="{{ route('student.learn', $order->course) }}" class="btn btn-primary w-100 py-2 fw-bold mb-3">
                <i class="bi bi-play-fill me-2"></i>Mulai Belajar Sekarang
            </a>
            <a href="{{ route('student.courses') }}" class="btn btn-outline-secondary w-100 py-2">
                Lihat Semua Kursus Saya
            </a>

            @else
            {{-- Payment not confirmed yet (webhook may arrive shortly) --}}
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle"
                     style="width:90px;height:90px;">
                    <i class="bi bi-hourglass-split text-warning" style="font-size:2.5rem;"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-2">Menunggu Konfirmasi</h3>
            <p class="text-muted mb-4">
                Pembayaran Anda sedang diproses. Halaman ini akan diperbarui otomatis.
                Nomor order: <strong>{{ $order->order_number }}</strong>
            </p>
            <a href="{{ route('student.courses') }}" class="btn btn-outline-primary w-100 py-2">
                Kembali ke Kursus Saya
            </a>

            <script>
                // Auto-refresh every 5 seconds to check payment status
                setTimeout(() => location.reload(), 5000);
            </script>
            @endif

        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Pembayaran Gagal')

@section('content')
<div class="container py-5" style="max-width:560px;">
    <div class="card border-0 shadow-sm text-center">
        <div class="card-body p-5">

            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle"
                     style="width:90px;height:90px;">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size:2.8rem;"></i>
                </div>
            </div>

            <h3 class="fw-bold mb-2">Pembayaran Gagal</h3>
            <p class="text-muted mb-4">
                Pembayaran untuk kursus <strong>{{ $order->course->title }}</strong> tidak berhasil
                atau dibatalkan. Silakan coba lagi.
            </p>

            <div class="bg-light rounded p-3 mb-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">No. Order</span>
                    <span class="small fw-semibold">{{ $order->order_number }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Status</span>
                    <span class="badge bg-danger">{{ ucfirst($order->status) }}</span>
                </div>
            </div>

            <a href="{{ route('checkout.show', $order->course) }}" class="btn btn-primary w-100 py-2 fw-bold mb-3">
                <i class="bi bi-arrow-clockwise me-2"></i>Coba Lagi
            </a>
            <a href="{{ route('courses.detail', $order->course->slug) }}" class="btn btn-outline-secondary w-100 py-2">
                Kembali ke Detail Kursus
            </a>

        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Verifikasi Tutor')
@section('page-title', 'Verifikasi Tutor')

@section('content')
<div class="card">
    <div class="card-header bg-white border-0">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item"><a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.tutors.index', ['status' => 'pending']) }}">Pending</a></li>
            <li class="nav-item"><a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.tutors.index', ['status' => 'approved']) }}">Approved</a></li>
            <li class="nav-item"><a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('admin.tutors.index', ['status' => 'rejected']) }}">Rejected</a></li>
        </ul>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="px-3">Tutor</th>
                    <th>Keahlian</th>
                    <th>Status</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tutors as $tutor)
                <tr>
                    <td class="px-3">
                        <strong>{{ $tutor->name }}</strong>
                        <p class="mb-0 text-muted small">{{ $tutor->email }}</p>
                    </td>
                    <td class="align-middle text-muted small">{{ Str::limit($tutor->tutorProfile?->expertise, 60) ?? '-' }}</td>
                    <td class="align-middle">
                        <span class="badge-status-{{ $tutor->tutorProfile?->verification_status }} px-2 py-1 rounded small">
                            {{ ucfirst($tutor->tutorProfile?->verification_status) }}
                        </span>
                    </td>
                    <td class="align-middle text-muted small">{{ $tutor->created_at->format('d M Y') }}</td>
                    <td class="align-middle">
                        <a href="{{ route('admin.tutors.show', $tutor) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        @if($tutor->tutorProfile?->verification_status === 'pending')
                        <form action="{{ route('admin.tutors.approve', $tutor) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada tutor dengan status {{ $status }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $tutors->links() }}</div>
</div>
@endsection

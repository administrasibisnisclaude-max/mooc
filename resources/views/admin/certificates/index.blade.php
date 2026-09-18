@extends('layouts.admin')
@section('title', 'Kelola Sertifikat')
@section('page-title', 'Kelola Sertifikat')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Daftar Sertifikat</h6>
        </div>
    </div>
    <div class="card-body border-bottom pb-3">
        <form action="{{ route('admin.certificates.index') }}" method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Cari nama atau nomor sertifikat...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th class="px-3">Pelajar</th><th>Kursus</th><th>No. Sertifikat</th><th>Diterbitkan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($certificates as $cert)
                <tr>
                    <td class="px-3 align-middle">{{ $cert->user->name }}</td>
                    <td class="align-middle small">{{ Str::limit($cert->course->title, 40) }}</td>
                    <td class="align-middle"><code>{{ $cert->certificate_number }}</code></td>
                    <td class="align-middle text-muted small">{{ $cert->issued_at?->format('d M Y') }}</td>
                    <td class="align-middle">
                        <a href="{{ route('student.certificate.show', $cert) }}" class="btn btn-sm btn-outline-info" target="_blank">Lihat</a>
                        <form action="{{ route('admin.certificates.destroy', $cert) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sertifikat ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada sertifikat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $certificates->links() }}</div>
</div>
@endsection

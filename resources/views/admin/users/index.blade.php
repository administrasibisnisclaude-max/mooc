@extends('layouts.admin')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<div class="card">
    <div class="card-header bg-white border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Daftar User</h6>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Tambah User</a>
        </div>
    </div>
    <div class="card-body border-bottom pb-3">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Cari nama atau email...">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="tutor" {{ request('role') === 'tutor' ? 'selected' : '' }}>Tutor</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="px-3">User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:14px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong class="small">{{ $user->name }}</strong>
                                <p class="mb-0 text-muted" style="font-size:11px;">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">
                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'tutor' ? 'bg-success' : 'bg-primary') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="align-middle">
                        @if($user->is_verified)
                            <span class="badge bg-success bg-opacity-10 text-success">Terverifikasi</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Belum</span>
                        @endif
                    </td>
                    <td class="align-middle text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="align-middle">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.users.toggle-verify', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_verified ? 'warning' : 'success' }}" title="{{ $user->is_verified ? 'Batalkan Verifikasi' : 'Verifikasi' }}">
                                <i class="bi bi-{{ $user->is_verified ? 'x-circle' : 'check-circle' }}"></i>
                            </button>
                        </form>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection

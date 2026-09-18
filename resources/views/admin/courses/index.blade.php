@extends('layouts.admin')
@section('title', 'Verifikasi Kursus')
@section('page-title', 'Verifikasi Kursus')

@section('content')
<div class="card">
    <div class="card-header bg-white border-0">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item"><a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.courses.index', ['status' => 'pending']) }}">Pending</a></li>
            <li class="nav-item"><a class="nav-link {{ $status === 'published' ? 'active' : '' }}" href="{{ route('admin.courses.index', ['status' => 'published']) }}">Published</a></li>
            <li class="nav-item"><a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('admin.courses.index', ['status' => 'rejected']) }}">Rejected</a></li>
            <li class="nav-item"><a class="nav-link {{ $status === 'draft' ? 'active' : '' }}" href="{{ route('admin.courses.index', ['status' => 'draft']) }}">Draft</a></li>
        </ul>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="px-3">Kursus</th>
                    <th>Tutor</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                <tr>
                    <td class="px-3">
                        <strong class="small">{{ Str::limit($course->title, 45) }}</strong>
                        <p class="mb-0 text-muted" style="font-size:11px;">{{ $course->created_at->format('d M Y') }}</p>
                    </td>
                    <td class="align-middle small">{{ $course->tutor->name }}</td>
                    <td class="align-middle small">{{ $course->category?->name ?? '-' }}</td>
                    <td class="align-middle small fw-semibold text-primary">{{ $course->formatted_price }}</td>
                    <td class="align-middle">
                        <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        @if($course->status === 'pending')
                        <form action="{{ route('admin.courses.approve', $course) }}" method="POST" class="d-inline">
                            @csrf <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada kursus dengan status {{ $status }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $courses->links() }}</div>
</div>
@endsection

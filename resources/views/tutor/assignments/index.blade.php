@extends('layouts.tutor')
@section('title', 'Tugas')
@section('page-title', 'Kelola Tugas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('tutor.courses.show', $course) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <a href="{{ route('tutor.assignments.create', $course) }}" class="btn btn-primary btn-sm">+ Buat Tugas</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th class="px-3">Tugas</th><th>Deadline</th><th>Max Nilai</th><th>Submission</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($assignments as $assign)
                <tr>
                    <td class="px-3">
                        <strong class="small">{{ $assign->title }}</strong>
                        <p class="mb-0 text-muted" style="font-size:11px;">{{ Str::limit($assign->description, 50) }}</p>
                    </td>
                    <td class="align-middle small">{{ $assign->due_date?->format('d M Y H:i') ?? 'Tidak ada' }}</td>
                    <td class="align-middle">{{ $assign->max_score }}</td>
                    <td class="align-middle">{{ $assign->submissions_count }}</td>
                    <td class="align-middle">
                        <a href="{{ route('tutor.grading.index', [$course, $assign]) }}" class="btn btn-sm btn-outline-success">Nilai</a>
                        <a href="{{ route('tutor.assignments.edit', [$course, $assign]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('tutor.assignments.destroy', [$course, $assign]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada tugas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

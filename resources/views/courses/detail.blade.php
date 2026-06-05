@extends('layouts.app')
@section('title', $course->title)

@section('content')
<div class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-white-50">Kursus</a></li>
                        <li class="breadcrumb-item active text-white">{{ Str::limit($course->title, 30) }}</li>
                    </ol>
                </nav>
                @if($course->category)
                    <span class="badge bg-primary mb-2">{{ $course->category->name }}</span>
                @endif
                <h1 class="fw-bold mb-3">{{ $course->title }}</h1>
                <p class="lead mb-3 opacity-75">{{ Str::limit($course->description, 150) }}</p>
                <div class="d-flex align-items-center flex-wrap gap-3 mb-3">
                    <span class="text-warning">
                        @php $avg = round($course->average_rating, 1) @endphp
                        @for($i=1; $i<=5; $i++)
                            <i class="bi {{ $i <= $avg ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                        <strong class="text-white ms-1">{{ number_format($avg, 1) }}</strong>
                    </span>
                    <span class="text-white-50">({{ $course->reviews->count() }} ulasan)</span>
                    <span class="text-white-50"><i class="bi bi-people me-1"></i>{{ $course->enrollments->count() }} pelajar</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($course->tutor->name) }}&size=35&background=0056D2&color=fff"
                        class="rounded-circle" alt="{{ $course->tutor->name }}">
                    <div>
                        <small class="text-white-50">Dibuat oleh</small>
                        <p class="mb-0 fw-semibold">{{ $course->tutor->name }}</p>
                    </div>
                </div>
                <div class="mt-3 d-flex flex-wrap gap-3">
                    <span><i class="bi bi-translate me-1"></i>{{ $course->language }}</span>
                    <span><i class="bi bi-bar-chart me-1"></i>{{ ucfirst($course->level) }}</span>
                    <span><i class="bi bi-collection-play me-1"></i>{{ $totalLessons }} Materi</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- What You'll Learn -->
            @if($course->what_youll_learn)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-3">Yang Akan Anda Pelajari</h4>
                    <div class="row g-2">
                        @foreach(explode("\n", $course->what_youll_learn) as $item)
                        @if(trim($item))
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                <span>{{ trim($item) }}</span>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Requirements -->
            @if($course->requirements)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-3">Persyaratan</h4>
                    @foreach(explode("\n", $course->requirements) as $item)
                        @if(trim($item))
                        <div class="d-flex gap-2 mb-2">
                            <i class="bi bi-dot fs-5 text-primary"></i>
                            <span>{{ trim($item) }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Curriculum -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-1">Kurikulum</h4>
                    <p class="text-muted mb-3">{{ $totalLessons }} materi &bull; Total {{ intdiv($totalDuration, 3600) }}j {{ intdiv($totalDuration % 3600, 60) }}m</p>
                    <div class="accordion" id="curriculumAccordion">
                        @foreach($course->sections as $section)
                        <div class="accordion-item border mb-2 rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#section{{ $section->id }}">
                                    {{ $section->title }}
                                    <span class="ms-auto me-3 text-muted small fw-normal">{{ $section->lessons->count() }} materi</span>
                                </button>
                            </h2>
                            <div id="section{{ $section->id }}" class="accordion-collapse collapse">
                                <div class="accordion-body p-0">
                                    @foreach($section->lessons as $lesson)
                                    <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                                        <i class="bi {{ $lesson->type === 'video' ? 'bi-play-circle text-primary' : ($lesson->type === 'document' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-text text-secondary') }}"></i>
                                        <span class="flex-grow-1">{{ $lesson->title }}</span>
                                        @if($lesson->is_free_preview)
                                            <span class="badge bg-success bg-opacity-10 text-success small">Preview</span>
                                        @endif
                                        @if($lesson->duration)
                                            <span class="text-muted small">{{ $lesson->formatted_duration }}</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            @if($course->reviews->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="fw-bold mb-3">Ulasan Pelajar</h4>
                    @foreach($course->reviews->take(5) as $review)
                    <div class="d-flex gap-3 mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&size=40&background=0056D2&color=fff"
                            class="rounded-circle" style="width:40px;height:40px;" alt="{{ $review->user->name }}">
                        <div>
                            <strong>{{ $review->user->name }}</strong>
                            <div class="text-warning small mb-1">
                                @for($i=1; $i<=5; $i++)<i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor
                            </div>
                            <p class="mb-0 text-muted">{{ $review->review }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sticky CTA Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg sticky-top" style="top: 90px;">
                <div class="card-body p-4">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" class="w-100 rounded mb-3" style="max-height:200px;object-fit:cover;" alt="">
                    @endif
                    <h3 class="fw-bold text-primary mb-3">{{ $course->formatted_price }}</h3>

                    @auth
                        @if($isEnrolled)
                            <a href="{{ route('student.learn', $course) }}" class="btn btn-success w-100 py-2 mb-2">
                                <i class="bi bi-play-fill me-2"></i>Lanjutkan Belajar
                            </a>
                            @if($enrollment)
                            <div class="mt-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progress</span>
                                    <span>{{ round($enrollment->progress_percentage) }}%</span>
                                </div>
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar" style="width:{{ $enrollment->progress_percentage }}%"></div>
                                </div>
                            </div>
                            @endif
                        @elseif(auth()->user()->isStudent())
                            <form action="{{ route('student.enroll', $course) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="bi bi-person-plus me-2"></i>Daftar Kursus
                                </button>
                            </form>
                        @else
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-primary w-100">Lihat Kursus Lain</a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary w-100 py-2 mb-2">
                            <i class="bi bi-person-plus me-2"></i>Daftar & Mulai Belajar
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Sudah Punya Akun</a>
                    @endauth

                    <hr>
                    <h6 class="fw-bold mb-3">Termasuk:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-collection-play text-primary me-2"></i>{{ $totalLessons }} materi</li>
                        <li class="mb-2"><i class="bi bi-infinity text-primary me-2"></i>Akses selamanya</li>
                        <li class="mb-2"><i class="bi bi-phone text-primary me-2"></i>Akses di semua perangkat</li>
                        <li class="mb-2"><i class="bi bi-award text-primary me-2"></i>Sertifikat kelulusan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

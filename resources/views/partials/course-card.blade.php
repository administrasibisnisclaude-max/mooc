<div class="card course-card h-100 border-0 shadow-sm">
    <a href="{{ route('courses.detail', $course->slug) }}" class="text-decoration-none">
        @if($course->thumbnail)
            <img src="{{ asset('storage/' . $course->thumbnail) }}" class="card-img-top" style="height:160px;object-fit:cover;" alt="{{ $course->title }}">
        @else
            <div class="bg-primary d-flex align-items-center justify-content-center text-white" style="height:160px;">
                <i class="bi bi-book fs-1"></i>
            </div>
        @endif
    </a>
    <div class="card-body d-flex flex-column">
        @if($course->category)
            <span class="badge bg-primary bg-opacity-10 text-primary small mb-2">{{ $course->category->name }}</span>
        @endif
        <h6 class="fw-bold mb-2 lh-sm">
            <a href="{{ route('courses.detail', $course->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($course->title, 55) }}</a>
        </h6>
        <p class="text-muted small mb-2">{{ $course->tutor->name ?? 'Tutor' }}</p>
        <div class="d-flex align-items-center gap-1 mb-2">
            @php $avg = round($course->average_rating, 1) @endphp
            <span class="text-warning small">
                @for($i=1; $i<=5; $i++)
                    <i class="bi {{ $i <= $avg ? 'bi-star-fill' : ($i - 0.5 <= $avg ? 'bi-star-half' : 'bi-star') }}"></i>
                @endfor
            </span>
            <small class="text-muted">({{ $course->reviews->count() }})</small>
        </div>
        <div class="d-flex align-items-center justify-content-between mt-auto">
            <span class="badge {{ $course->level === 'beginner' ? 'bg-success' : ($course->level === 'intermediate' ? 'bg-warning text-dark' : 'bg-danger') }} bg-opacity-15 {{ $course->level === 'beginner' ? 'text-success' : ($course->level === 'intermediate' ? 'text-warning' : 'text-danger') }}">
                {{ ucfirst($course->level) }}
            </span>
            <strong class="text-primary">{{ $course->formatted_price }}</strong>
        </div>
    </div>
</div>

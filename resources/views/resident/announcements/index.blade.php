@extends('layouts.app')

@section('title', 'Barangay Announcements')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Barangay Announcements</h3>
        <p class="text-muted small mb-0">Stay updated with the latest news, events, and advisories.</p>
    </div>

    <div class="row g-4">
        @forelse($announcements as $item)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Announcement">
                    @endif
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-2">
                            @if($item->priority == 'urgent')
                                <span class="badge bg-danger rounded-pill px-3">Urgent</span>
                            @else
                                <span class="badge bg-primary rounded-pill px-3">General</span>
                            @endif
                            <span class="text-muted small ms-2">{{ $item->created_at->format('M d, Y') }}</span>
                        </div>

                        <h5 class="fw-bold text-dark mb-2">{{ $item->title }}</h5>

                        <p class="text-muted small mb-4">
                            {{ Str::limit($item->body, 100) }}
                        </p>

                        <a href="{{ route('resident.announcements.show', $item->id) }}" class="btn btn-outline-primary btn-sm rounded-pill mt-auto">
                            Read More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-megaphone fs-1 text-muted d-block mb-2"></i>
                <p class="text-muted">There are currently no announcements available.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
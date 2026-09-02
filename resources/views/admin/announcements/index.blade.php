@extends('layouts.admin')

@section('title', 'Manage Announcements')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Manage Announcements</h1>
            <p class="text-muted small mb-0">Post and manage official barangay news, events, and advisories.</p>
        </div>
        <div>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary px-3 py-2 rounded-3 small fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Post Announcement
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card Container -->
    <div class="card border-0 shadow-sm rounded-4 p-4" style="min-height: 750px; padding-bottom: 120px !important;">
        <div class="table-responsive overflow-visible">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-3 ps-3 rounded-start-3">Announcement Title</th>
                        <th class="py-3">Priority Level</th>
                        <th class="py-3">Date Posted</th>
                        <th class="py-3 text-end pe-3 rounded-end-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $announcement)
                    <tr class="border-bottom border-light">
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-3">
                                @if($announcement->image)
                                    <img src="{{ asset('storage/' . $announcement->image) }}" alt="" class="rounded-3 object-fit-cover" style="width: 42px; height: 42px;">
                                @else
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 42px; height: 42px;">
                                        <i class="bi bi-megaphone fs-5"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold text-dark">{{ $announcement->title }}</div>
                                    <div class="text-muted small">{{ Str::limit($announcement->body ?? $announcement->content, 40) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $priority = strtolower($announcement->priority ?? 'normal');
                                $badgeBg = 'bg-warning text-dark';
                            @endphp
                            <span class="badge {{ $badgeBg }} rounded-pill px-3 py-2 fw-semibold text-uppercase d-inline-flex align-items-center shadow-sm" style="font-size: 0.7rem;">
                                <span class="spinner-grow spinner-grow-sm me-1 bg-dark" style="width: 5px; height: 5px;" role="status"></span>
                                {{ $announcement->priority ?? 'Important' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-secondary small fw-medium">
                                <i class="bi bi-calendar3 text-muted"></i>
                                <span>{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-circle shadow-sm p-1" style="width: 32px; height: 32px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2">
                                    <li>
                                        <a class="dropdown-item py-2 px-3 text-dark small fw-medium" href="{{ route('admin.announcements.show', $announcement->id) }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 px-3 text-dark small fw-medium" href="{{ route('admin.announcements.edit', $announcement->id) }}">
                                            <i class="bi bi-pencil-square me-2 text-warning"></i> Edit Announcement
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-2 px-3 text-danger small fw-medium">
                                                <i class="bi bi-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-megaphone fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No announcements found</h6>
                                <p class="small text-muted mb-0">There are currently no announcements posted.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-end">
            @if(method_exists($announcements, 'withQueryString'))
                {{ $announcements->withQueryString()->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Announcement Details - Admin Panel')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Announcement Details</h1>
            <p class="text-muted small mb-0">Viewing full information and preview of the announcement.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-light border shadow-sm px-3 py-2 rounded-3 small fw-medium text-dark d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="btn btn-warning px-3 py-2 rounded-3 small fw-semibold d-inline-flex align-items-center gap-2 text-dark shadow-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Meta / Priority and Date -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-3 border-bottom">
                    <div>
                        @php
                            $priority = strtolower($announcement->priority ?? 'normal');
                            $badgeClass = 'bg-secondary';
                            if ($priority == 'important') $badgeClass = 'bg-warning text-dark';
                            if ($priority == 'urgent') $badgeClass = 'bg-danger text-white';
                        @endphp
                        <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">
                            {{ ucfirst($priority) }} Priority
                        </span>
                    </div>
                    <div class="text-muted small fw-medium">
                        <i class="bi bi-calendar3 me-1"></i> Posted on {{ $announcement->created_at->format('F d, Y h:i A') }}
                        @if($announcement->user)
                            <span class="ms-2"><i class="bi bi-person me-1"></i> By {{ $announcement->user->name }}</span>
                        @endif
                    </div>
                </div>

                <!-- Title -->
                <h2 class="fw-bold text-dark mb-4">{{ $announcement->title }}</h2>

                <!-- Image Preview if available -->
                @if($announcement->image)
                    <div class="mb-4 rounded-4 overflow-hidden border shadow-sm bg-light text-center" style="max-height: 400px;">
                        <img src="{{ filter_var($announcement->image, FILTER_VALIDATE_URL) ? $announcement->image : asset('storage/' . $announcement->image) }}" 
                             alt="Announcement Image" 
                             class="img-fluid w-100 object-fit-contain" 
                             style="max-height: 400px;">
                    </div>
                @endif

                <!-- Body / Content -->
                <div class="text-dark lh-lg fs-6" style="white-space: pre-line;">
                    {{ $announcement->body }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
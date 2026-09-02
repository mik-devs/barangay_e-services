@extends('layouts.app')

@section('title', $announcement->title . ' - Barangay E-Portal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="mb-4">
                <a href="{{ route('resident.announcements.index') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Announcements
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden p-4">
                <div class="mb-3">
                    @if($announcement->priority == 'urgent')
                        <span class="badge bg-danger rounded-pill px-3 py-2">Urgent Notice</span>
                    @else
                        <span class="badge bg-primary rounded-pill px-3 py-2">General Announcement</span>
                    @endif
                    <span class="text-muted small ms-2"><i class="bi bi-calendar3 me-1"></i> {{ $announcement->created_at->format('F d, Y h:i A') }}</span>
                </div>

                <h2 class="fw-bold text-dark mb-3">{{ $announcement->title }}</h2>

                @if($announcement->image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $announcement->image) }}" class="img-fluid rounded-4 w-100" style="max-height: 400px; object-fit: cover;" alt="Announcement Banner">
                    </div>
                @endif

                <div class="text-dark lh-lg" style="white-space: pre-line;">
                    {{ $announcement->body }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Incident Details - Barangay E-Portal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Incident Details</h3>
                    <p class="text-muted small mb-0">Report Reference: <span class="fw-bold text-primary">{{ $incident->report_number }}</span></p>
                </div>
                <a href="{{ route('resident.incidents.index') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to History
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Incident Type</span>
                        <h6 class="fw-bold text-dark">{{ $incident->incident_type }}</h6>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Status</span>
                        @if($incident->status == 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
                        @elseif($incident->status == 'resolved')
                            <span class="badge bg-success px-3 py-2 rounded-pill">Resolved</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ ucfirst($incident->status) }}</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Location</span>
                        <p class="fw-semibold text-dark mb-0">{{ $incident->location }}</p>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Date & Time Occurred</span>
                        <p class="fw-semibold text-dark mb-0">{{ \Carbon\Carbon::parse($incident->incident_date)->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Description</span>
                        <p class="text-dark bg-light p-3 rounded-3 mb-0">{{ $incident->description }}</p>
                    </div>
                    @if($incident->attachment)
                        <div class="col-12">
                            <span class="text-muted small d-block mb-1">Attachment Evidence</span>
                            <a href="{{ asset('storage/' . $incident->attachment) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill">
                                <i class="bi bi-image me-1"></i> View Uploaded Image
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
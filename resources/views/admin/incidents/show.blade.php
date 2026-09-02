@extends('layouts.admin')

@section('title', 'Incident Report Details')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Incident Report Details</h1>
            <p class="text-muted small mb-0">Review incident report information and update its status.</p>
        </div>
        <div>
            <a href="{{ route('admin.incidents.index') }}" class="btn btn-light border fw-semibold text-secondary px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Incidents
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Details Column -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-4">
                    <div>
                        <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.70rem; letter-spacing: 0.5px;">Report Number</span>
                        <span class="fw-bold text-primary fs-5">{{ $incident->report_number }}</span>
                    </div>
                    @php
                        $status = strtolower(trim($incident->status ?? 'pending'));
                        
                        // UI Badge color definitions
                        $badgeBg = 'bg-warning text-dark';
                        $dotColor = 'bg-dark';
                        
                        if ($status == 'resolved') {
                            $badgeBg = 'bg-success text-white';
                            $dotColor = 'bg-white';
                        } elseif ($status == 'investigating') {
                            $badgeBg = 'bg-info text-dark';
                            $dotColor = 'bg-dark';
                        } elseif ($status == 'rejected') {
                            $badgeBg = 'bg-danger text-white';
                            $dotColor = 'bg-white';
                        }
                    @endphp
                    <span class="badge {{ $badgeBg }} rounded-pill px-3 py-2 fw-semibold text-capitalize d-inline-flex align-items-center shadow-sm">
                        <span class="spinner-grow spinner-grow-sm me-1 {{ $dotColor }}" style="width: 6px; height: 6px;" role="status"></span>
                        {{ ucfirst($incident->status ?? 'Pending') }}
                    </span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Incident Type</span>
                        <span class="fw-bold text-dark fs-6">{{ $incident->incident_type }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Location</span>
                        <div class="d-flex align-items-center gap-1 text-dark fw-medium">
                            <i class="bi bi-geo-alt text-danger"></i>
                            <span>{{ $incident->location ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Reported By</span>
                        <span class="fw-bold text-dark">
                            {{ trim(($resident->first_name ?? '') . ' ' . ($resident->last_name ?? '')) ?: ($resident->name ?? 'N/A') }}
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Email Address</span>
                        <div class="d-flex align-items-center gap-1 text-secondary fw-medium">
                            <i class="bi bi-envelope text-primary"></i>
                            <span>{{ $resident->email ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Phone Number</span>
                        <div class="d-flex align-items-center gap-1 text-dark fw-medium">
                            <i class="bi bi-telephone text-success"></i>
                            <span>{{ $resident->phone_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Incident Date & Time</span>
                        <div class="d-flex align-items-center gap-1 text-dark fw-medium">
                            <i class="bi bi-calendar3 text-muted"></i>
                            <span>{{ $incident->incident_date ? \Carbon\Carbon::parse($incident->incident_date)->format('F d, Y h:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="d-block text-muted small fw-semibold text-uppercase mb-2" style="font-size: 0.7rem;">Description</span>
                    <div class="p-3 bg-light rounded-3 text-dark small lh-base border">
                        {{ $incident->description }}
                    </div>
                </div>

                @if(!empty($incident->attachment))
                    <div class="mb-4">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-2" style="font-size: 0.7rem;">Attachment Evidence</span>
                        <div>
                            <a href="{{ asset('storage/' . $incident->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-image me-1"></i> View Attachment Image
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar / Status Update Column -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <h5 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Update Incident Status</h5>
                <form action="{{ route('admin.incidents.update-status', $incident->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <label for="status" class="form-label small fw-bold text-dark">Status</label>
                        <select name="status" id="status" class="form-select bg-light shadow-none">
                            <option value="pending" {{ old('status', $incident->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="investigating" {{ old('status', $incident->status) == 'investigating' ? 'selected' : '' }}>Investigating</option>
                            <option value="resolved" {{ old('status', $incident->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="rejected" {{ old('status', $incident->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="admin_remarks" class="form-label small fw-bold text-dark">Admin Remarks</label>
                        <textarea name="admin_remarks" id="admin_remarks" rows="3" class="form-control bg-light shadow-none" placeholder="Add remarks or notes...">{{ old('admin_remarks', $incident->admin_remarks) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold py-2 rounded-pill shadow-sm">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
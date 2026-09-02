@extends('layouts.app')

@section('title', 'Booking Details - Barangay E-Portal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Booking Details</h3>
                    <p class="text-muted small mb-0">Reference Number: <span class="fw-bold text-info"><code>{{ $booking->reference_number }}</code></span></p>
                </div>
                <a href="{{ route('resident.bookings.index') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to History
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Facility Name</span>
                        <h5 class="fw-bold text-dark">{{ $booking->facility_name }}</h5>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Status</span>
                        @if($booking->status == 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
                        @elseif($booking->status == 'approved')
                            <span class="badge bg-success px-3 py-2 rounded-pill">Approved</span>
                        @elseif($booking->status == 'rejected')
                            <span class="badge bg-danger px-3 py-2 rounded-pill">Rejected</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Reserved Date</span>
                        <p class="fw-semibold text-dark mb-0">{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Time Slot</span>
                        <p class="fw-semibold text-dark mb-0">{{ date('h:i A', strtotime($booking->start_time)) }} to {{ date('h:i A', strtotime($booking->end_time)) }}</p>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Purpose of Reservation</span>
                        <p class="text-dark bg-light p-3 rounded-3 mb-0">{{ $booking->purpose }}</p>
                    </div>

                    @if($booking->admin_remarks)
                        <div class="col-12 mt-3">
                            <div class="alert alert-secondary rounded-3 mb-0">
                                <span class="fw-bold d-block mb-1"><i class="bi bi-chat-left-text me-1"></i> Barangay Official Feedback:</span>
                                <p class="mb-0">{{ $booking->admin_remarks }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
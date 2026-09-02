@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Back Button -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Booking Details #{{ $facilityBooking->id }}</h1>
            <p class="text-muted small mb-0">Review facility reservation details and manage its approval status.</p>
        </div>
        <div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-light border fw-semibold text-secondary px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Bookings
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Details Column -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-4">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Reservation Status</span>
                    @php
                        $status = strtolower(trim($facilityBooking->status ?? 'pending'));
                        
                        $badgeBg = 'bg-secondary text-white';
                        $dotColor = 'bg-white';
                        
                        if ($status == 'pending') {
                            $badgeBg = 'bg-warning text-dark';
                            $dotColor = 'bg-dark';
                        } elseif ($status == 'approved' || $status == 'verified') {
                            $badgeBg = 'bg-success text-white';
                            $dotColor = 'bg-white';
                        } elseif ($status == 'rejected' || $status == 'cancelled') {
                            $badgeBg = 'bg-danger text-white';
                            $dotColor = 'bg-white';
                        }
                    @endphp
                    <span class="badge {{ $badgeBg }} rounded-pill px-3 py-2 fw-semibold text-capitalize d-inline-flex align-items-center shadow-sm">
                        <span class="spinner-grow spinner-grow-sm me-1 {{ $dotColor }}" style="width: 6px; height: 6px;" role="status"></span>
                        {{ ucfirst($facilityBooking->status ?? 'Pending') }}
                    </span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Reference Number</span>
                        <span class="fw-bold text-primary font-monospace fs-6">{{ $facilityBooking->reference_number ?? 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Facility Name</span>
                        <span class="fw-bold text-dark fs-6">{{ $facilityBooking->facility_name }}</span>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Booking Date</span>
                        <div class="d-flex align-items-center gap-1 text-dark fw-medium">
                            <i class="bi bi-calendar3 text-primary"></i>
                            <span>{{ $facilityBooking->booking_date ? \Carbon\Carbon::parse($facilityBooking->booking_date)->format('F d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Time Schedule</span>
                        <div class="d-flex align-items-center gap-1 text-dark fw-medium">
                            <i class="bi bi-clock text-success"></i>
                            <span>
                                {{ $facilityBooking->start_time ? \Carbon\Carbon::parse($facilityBooking->start_time)->format('h:i A') : '' }} 
                                - 
                                {{ $facilityBooking->end_time ? \Carbon\Carbon::parse($facilityBooking->end_time)->format('h:i A') : '' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Display Amount if already set/approved -->
                @if($facilityBooking->amount)
                <div class="mb-4">
                    <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Approved Payment Amount</span>
                    <h4 class="fw-bold text-success mb-0">₱{{ number_format($facilityBooking->amount, 2) }}</h4>
                </div>
                @endif

                <div class="mb-3">
                    <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Purpose of Reservation</span>
                    <div class="p-3 bg-light rounded-3 text-dark small lh-base border">
                        {{ $facilityBooking->purpose }}
                    </div>
                </div>

                <!-- Admin Remarks kung na-reject -->
                @if(!empty($facilityBooking->admin_remarks))
                    <div class="mt-4">
                        <span class="d-block text-danger small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Admin Remarks / Rejection Reason</span>
                        <div class="p-3 bg-danger bg-opacity-10 rounded-3 text-danger small lh-base border border-danger border-opacity-20 fw-medium">
                            <i class="bi bi-info-circle-fill me-1"></i> {{ $facilityBooking->admin_remarks }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar / Resident & Actions Column -->
        <div class="col-lg-4">
            <!-- Resident Info Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Resident Information</h5>
                @if($facilityBooking->user)
                    <div class="vstack gap-2">
                        <div>
                            <span class="d-block text-muted" style="font-size: 0.75rem;">Full Name</span>
                            <span class="fw-bold text-dark">
                                {{ trim(($facilityBooking->user->first_name ?? '') . ' ' . ($facilityBooking->user->middle_name ?? '') . ' ' . ($facilityBooking->user->last_name ?? '')) ?: $facilityBooking->user->name }}
                            </span>
                        </div>
                        <div>
                            <span class="d-block text-muted" style="font-size: 0.75rem;">Email Address</span>
                            <span class="text-secondary fw-medium small">{{ $facilityBooking->user->email }}</span>
                        </div>
                        <div>
                            <span class="d-block text-muted" style="font-size: 0.75rem;">Phone Number</span>
                            <span class="text-dark fw-medium small">{{ $facilityBooking->user->phone_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                @else
                    <span class="text-muted small">No resident profile attached.</span>
                @endif
            </div>

            <!-- Action Controls Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Manage Booking</h5>
                
                <div class="vstack gap-2">
                    @if(strtolower($facilityBooking->status) != 'approved')
                        <!-- Magbubukas ng Approve Modal kung saan iti-type ang Bayad -->
                        <button type="button" class="btn btn-success btn-sm w-100 fw-semibold py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#approveBookingModal">
                            <i class="bi bi-check-circle me-1"></i> Approve Booking & Set Fee
                        </button>
                    @endif

                    @if(strtolower($facilityBooking->status) != 'rejected')
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-semibold py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#rejectBookingModal">
                            <i class="bi bi-x-circle me-1"></i> Reject Booking
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 1. APPROVE MODAL (May Set Payment Amount Input) -->
<div class="modal fade" id="approveBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.bookings.update-status', $facilityBooking->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="approved">
                
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-success fs-6">Set Fee & Approve Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="amount" class="form-label small fw-bold text-dark">Payment Amount (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">₱</span>
                            <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control bg-light shadow-none fw-semibold" value="{{ $facilityBooking->amount ?? '0.00' }}" placeholder="0.00" required>
                        </div>
                        <div class="form-text text-muted small mt-1">
                            Enter the amount the resident is required to pay for this facility.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3 btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 btn-sm fw-semibold">Confirm Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. REJECT MODAL -->
<div class="modal fade" id="rejectBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.bookings.update-status', $facilityBooking->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-danger fs-6">Reject Facility Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="admin_remarks" class="form-label small fw-bold text-dark">Reason for Rejection / Remarks</label>
                        <textarea name="admin_remarks" id="admin_remarks" rows="3" class="form-control bg-light shadow-none" placeholder="For example: It is no longer vacant because another event is scheduled." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3 btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 btn-sm fw-semibold">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Facility Bookings Management')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Facility Bookings Management</h1>
            <p class="text-muted small mb-0">Monitor and manage all resident facility reservation requests.</p>
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
        
        <!-- Search and Filter Bar -->
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3 mb-4 align-items-center justify-content-between" id="filterForm">
            <input type="hidden" name="facility" id="facilityInput" value="{{ request('facility', 'all') }}">
            
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 shadow-none" placeholder="Search resident, facility, or purpose..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-filter"></i></span>
                    <select name="status" class="form-select bg-light border-start-0 shadow-none" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Facility Filter Tabs -->
        @php
            $currentFacility = request('facility', 'all');
            $facilities = ['All Facilities', 'Covered Court', 'Multi-Purpose Hall', 'Barangay Health Center', 'Evacuation Center'];
        @endphp
        <div class="d-flex flex-wrap gap-2 mb-4 pb-2 border-bottom">
            @foreach($facilities as $fac)
                @php
                    $facValue = $fac === 'All Facilities' ? 'all' : $fac;
                    $isActive = $currentFacility === $facValue;
                @endphp
                <button type="button" 
                        onclick="document.getElementById('facilityInput').value='{{ $facValue }}'; document.getElementById('filterForm').submit();"
                        class="btn btn-sm rounded-pill px-3 py-2 fw-medium transition {{ $isActive ? 'btn-primary shadow-sm' : 'btn-light text-secondary border' }}">
                    {{ $fac }}
                </button>
            @endforeach
        </div>

        <div class="table-responsive overflow-visible">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-3 ps-3 rounded-start-3">Booking ID</th>
                        <th class="py-3">Resident</th>
                        <th class="py-3">Facility Name</th>
                        <th class="py-3">Purpose</th>
                        <th class="py-3">Date & Time</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-3 rounded-end-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-bottom border-light">
                        <td class="ps-3 font-monospace text-primary fw-bold">#{{ $booking->id }}</td>
                        <td>
                            <div class="fw-semibold text-dark">
                                @if($booking->user)
                                    {{ trim(($booking->user->first_name ?? '') . ' ' . ($booking->user->middle_name ?? '') . ' ' . ($booking->user->last_name ?? '')) ?: $booking->user->name }}
                                @else
                                    <span class="text-muted fw-normal">N/A</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ $booking->facility_name }}</div>
                        </td>
                        <td>
                            <span class="text-muted small" title="{{ $booking->purpose }}">
                                {{ Str::limit($booking->purpose, 30) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-secondary small fw-medium">
                                <i class="bi bi-calendar3 text-muted"></i>
                                <span>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $status = strtolower($booking->status);
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
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-circle shadow-sm p-1" style="width: 32px; height: 32px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2">
                                    <li>
                                        <a class="dropdown-item py-2 px-3 text-dark small fw-medium" href="{{ route('admin.bookings.show', $booking->id) }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> View Details
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="dropdown-item py-2 px-3 text-success small fw-medium">
                                                <i class="bi bi-check-circle me-2"></i> Approve Booking
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="dropdown-item py-2 px-3 text-danger small fw-medium">
                                                <i class="bi bi-x-circle me-2"></i> Reject Booking
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-calendar-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No facility bookings found</h6>
                                <p class="small text-muted mb-0">There are currently no facility reservation requests matching your filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-end">
            {{ $bookings->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
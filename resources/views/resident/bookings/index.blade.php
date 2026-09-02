@extends('layouts.app')

@section('title', 'My Facility Bookings - Barangay E-Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">My Facility Bookings</h3>
            <p class="text-muted small mb-0">Track the status of your barangay facility reservations.</p>
        </div>
        <a href="{{ route('resident.bookings.create') }}" class="btn btn-info text-white rounded-pill px-4 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Reserve Facility
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- FILTER TABS -->
    @php
        $currentFacility = request('facility', 'all');
    @endphp
    <div class="mb-3 overflow-auto">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $currentFacility == 'all' ? 'active shadow-sm bg-info text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('resident.bookings.index', ['facility' => 'all']) }}">
                   All Facilities
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $currentFacility == 'Covered Court' ? 'active shadow-sm bg-info text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('resident.bookings.index', ['facility' => 'Covered Court']) }}">
                   Covered Court
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $currentFacility == 'Multi-Purpose Hall' ? 'active shadow-sm bg-info text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('resident.bookings.index', ['facility' => 'Multi-Purpose Hall']) }}">
                   Multi-Purpose Hall
                </a>
            </li>
        </ul>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Reference No.</th>
                        <th class="py-3">Facility</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Time</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="fw-bold text-info"><code>{{ $booking->reference_number }}</code></td>
                            <td class="fw-semibold text-dark">{{ $booking->facility_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</td>
                            <td>{{ date('h:i A', strtotime($booking->start_time)) }} - {{ date('h:i A', strtotime($booking->end_time)) }}</td>
                            <td>
                                @if($booking->status == 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
                                @elseif($booking->status == 'approved')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Approved</span>
                                @elseif($booking->status == 'rejected')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">Rejected</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('resident.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                You have not made any facility reservations yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $bookings->appends(['facility' => $currentFacility])->links() }}
        </div>
    </div>
</div>
@endsection
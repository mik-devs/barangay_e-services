@extends('layouts.app')

@section('title', 'My Payments')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark mb-1">My Payments & Fees</h1>
        <p class="text-muted small mb-0">View and pay fees for your approved document requests and facility bookings.</p>
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

    <!-- Filter Tabs (All, Documents, Facility) -->
    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('resident.payments.index') }}" class="btn btn-sm {{ request('type') == null ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill px-3 fw-semibold">
            All Payments
        </a>
        <a href="{{ route('resident.payments.index', ['type' => 'document']) }}" class="btn btn-sm {{ request('type') == 'document' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 fw-semibold">
            Documents
        </a>
        <a href="{{ route('resident.payments.index', ['type' => 'facility']) }}" class="btn btn-sm {{ request('type') == 'facility' ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3 fw-semibold">
            Facility Bookings
        </a>
    </div>

    <!-- Payments Table Card -->
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7" style="font-size: 0.75rem;">
                    <tr>
                        <th class="py-3">Reference / Transaction</th>
                        <th class="py-3">Details / Type</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php
                            // Tukuyin nang sigurado kung FacilityPayment ba ito o regular Payment
                            $isFacility = ($payment instanceof \App\Models\FacilityPayment) || isset($payment->facility_booking_id);
                            
                            $typeSlug = $isFacility ? 'facility' : 'document';
                            $payableId = $isFacility ? $payment->facility_booking_id : $payment->payable_id;
                        @endphp
                        <tr>
                            <!-- Reference Number -->
                            <td class="fw-bold text-dark">
                                {{ $payment->reference_number }}
                                <div class="text-muted small fw-normal" style="font-size: 0.75rem;">
                                    {{ $payment->created_at ? $payment->created_at->format('M d, Y h:i A') : '' }}
                                </div>
                            </td>

                            <!-- Details depending on Type -->
                            <td>
                                @if($isFacility)
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill fw-semibold">
                                        Facility Booking
                                    </span>
                                    <div class="small text-secondary mt-1">
                                        <strong>{{ $payment->facilityBooking->facility_name ?? 'Facility' }}</strong>
                                        <span class="text-muted d-block" style="font-size: 0.75rem;">
                                            Date: {{ $payment->facilityBooking->booking_date ? \Carbon\Carbon::parse($payment->facilityBooking->booking_date)->format('M d, Y') : '' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill fw-semibold">
                                        Document Request
                                    </span>
                                    <div class="small text-secondary mt-1">
                                        {{ $payment->payable->document_type ?? 'Standard Document' }} 
                                        <span class="text-muted">({{ $payment->payable->tracking_number ?? '' }})</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="fw-bold text-dark">
                                ₱{{ number_format($payment->amount, 2) }}
                            </td>

                            <!-- Payment Status -->
                            <td>
                                @php
                                    $status = strtolower($payment->payment_status);
                                    
                                    $statusClasses = [
                                        'paid'                => 'bg-success-subtle text-success border border-success-subtle',
                                        'pending'             => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                        'waiting_for_payment' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                        'failed'              => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        'cancelled'           => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                    ];
                                    
                                    $currentClass = $statusClasses[$status] ?? 'bg-info-subtle text-info border border-info-subtle';
                                @endphp
                                <span class="badge {{ $currentClass }} px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 0.7rem;">
                                    {{ ucwords(str_replace('_', ' ', $payment->payment_status)) }}
                                </span>
                            </td>

                            <!-- Action Button -->
                            <td class="text-end">
                                @if($payment->payment_status === 'paid')
                                    <a href="{{ route('resident.payments.receipt', $payment->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-receipt me-1"></i> View Receipt
                                    </a>
                                @else
                                    <a href="{{ route('resident.payments.checkout', ['type' => $typeSlug, 'id' => $payableId]) }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                                        <i class="bi bi-credit-card me-1"></i> Pay Now
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-2 d-block mb-2 opacity-50"></i>
                                No payments found for this category.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
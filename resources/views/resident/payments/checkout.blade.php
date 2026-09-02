@extends('layouts.app')

@section('title', 'Payment Checkout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-5 bg-white" style="border-radius: 1rem;">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1 tracking-tight">Complete Your Payment</h4>
                        <p class="text-muted small mb-0">Select your preferred payment gateway or choose cash at the barangay hall.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                        Secure Checkout
                    </span>
                </div>

                <!-- Summary Details -->
                <div class="bg-light p-4 rounded-4 mb-4">
                    <h6 class="fw-bold text-dark mb-3">Transaction Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Service Type:</span>
                        <span class="fw-semibold text-dark">
                            {{ $type === 'document' ? 'Document Request' : 'Facility Booking' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Reference Item ID:</span>
                        <span class="font-monospace text-dark">#{{ $payableModel->id }}</span>
                    </div>
                    
                    @if($type === 'facility')
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Facility Name:</span>
                        <span class="fw-semibold text-dark">{{ $payableModel->facility_name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Booking Date:</span>
                        <span class="fw-semibold text-dark">{{ $payableModel->booking_date ?? 'N/A' }}</span>
                    </div>
                    @else
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Document Type:</span>
                        <span class="fw-semibold text-dark">{{ $payableModel->document_type ?? 'N/A' }}</span>
                    </div>
                    @endif

                    <hr class="text-muted opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Total Amount Due:</span>
                        <span class="fs-4 fw-bold text-primary">
                            @php
                                $amountDue = 0.00;
                                if (isset($paymentRecord) && $paymentRecord->amount) {
                                    $amountDue = $paymentRecord->amount;
                                } else {
                                    $amountDue = $type === 'document' ? ($payableModel->fee ?? 100.00) : ($payableModel->total_amount ?? 300.00);
                                }
                            @endphp
                            ₱{{ number_format($amountDue, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ route('resident.payments.process', [$type, $payableModel->id]) }}" method="POST">
                    @csrf
                    <h6 class="fw-bold text-dark mb-3">Choose Payment Method</h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="payment_method" id="gcash" value="gcash" checked>
                            <label class="btn btn-outline-light text-dark border w-100 p-3 text-start d-flex align-items-center shadow-sm" for="gcash" style="border-radius: 0.75rem;">
                                <i class="bi bi-wallet2 fs-4 text-primary me-3"></i>
                                <div>
                                    <span class="fw-bold d-block">GCash</span>
                                    <small class="text-muted">Pay via GCash mobile wallet</small>
                                </div>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="payment_method" id="maya" value="maya">
                            <label class="btn btn-outline-light text-dark border w-100 p-3 text-start d-flex align-items-center shadow-sm" for="maya" style="border-radius: 0.75rem;">
                                <i class="bi bi-credit-card fs-4 text-success me-3"></i>
                                <div>
                                    <span class="fw-bold d-block">Maya</span>
                                    <small class="text-muted">Pay via Maya digital wallet</small>
                                </div>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="payment_method" id="qrph" value="qrph">
                            <label class="btn btn-outline-light text-dark border w-100 p-3 text-start d-flex align-items-center shadow-sm" for="qrph" style="border-radius: 0.75rem;">
                                <i class="bi bi-qr-code fs-4 text-dark me-3"></i>
                                <div>
                                    <span class="fw-bold d-block">QRPH</span>
                                    <small class="text-muted">Universal QR payment</small>
                                </div>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="payment_method" id="cash" value="cash">
                            <label class="btn btn-outline-light text-dark border w-100 p-3 text-start d-flex align-items-center shadow-sm" for="cash" style="border-radius: 0.75rem;">
                                <i class="bi bi-cash-stack fs-4 text-warning me-3"></i>
                                <div>
                                    <span class="fw-bold d-block">Cash at Barangay Hall</span>
                                    <small class="text-muted">Pay directly over-the-counter</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('resident.payments.index') }}" class="text-muted text-decoration-none small">
                            <i class="bi bi-arrow-left me-1"></i> Back to payments
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="border-radius: 0.75rem;">
                            Proceed to Pay <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
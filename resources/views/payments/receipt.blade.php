@extends('layouts.app')

@section('title', 'Official Receipt - ' . ($payment->or_number ?? $payment->reference_number))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-5 bg-white" style="border-radius: 1rem;">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary bg-gradient p-2 rounded-3 text-white me-2 shadow-sm" style="border-radius: 1rem;">
                                <i class="bi bi-bank fs-4"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-0 tracking-tight">Barangay E-Services Official Receipt</h4>
                        </div>
                        <p class="text-muted small mb-0">Official Government Digital Transaction Record</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                            {{ strtoupper($payment->payment_status) }}
                        </span>
                        <div class="font-monospace text-muted small mt-2">OR #: {{ $payment->or_number ?? 'PENDING-OR' }}</div>
                    </div>
                </div>

                <!-- Resident & Transaction Info -->
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <span class="text-muted small text-uppercase d-block fw-semibold mb-1">Billed To</span>
                        <h6 class="fw-bold text-dark mb-1">{{ optional($payment->resident)->name ?? 'Resident' }}</h6>
                        <span class="text-muted small">Ref No: <span class="font-monospace text-dark fw-bold">{{ $payment->reference_number }}</span></span>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        <span class="text-muted small text-uppercase d-block fw-semibold mb-1">Transaction Date</span>
                        <h6 class="fw-bold text-dark mb-1">{{ optional($payment->paid_at)->format('M d, Y h:i A') ?? $payment->created_at->format('M d, Y h:i A') }}</h6>
                        <span class="text-muted small">Method: <span class="text-uppercase fw-semibold">{{ str_replace('_', ' ', $payment->payment_method) }}</span></span>
                    </div>
                </div>

                <!-- Table Summary -->
                <div class="table-responsive mb-4">
                    <table class="table align-middle border rounded-3 overflow-hidden">
                        <thead class="table-light text-uppercase fs-7 text-secondary">
                            <tr>
                                <th class="py-3 ps-3">Service Details</th>
                                <th class="py-3 text-end pe-3">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3 py-3">
                                    <span class="fw-bold text-dark d-block">{{ class_basename($payment->payable_type) }} Service Fee</span>
                                    <small class="text-muted">Standard barangay processing and online portal documentation fee.</small>
                                </td>
                                <td class="text-end pe-3 fw-bold text-dark">₱{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td class="ps-3 fw-bold text-dark py-3">Total Paid</td>
                                <td class="text-end pe-3 fw-bold text-primary fs-5 py-3">₱{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- QR Verification & Action Buttons -->
                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between bg-light p-4 rounded-4 mt-2 gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white p-2 rounded-3 border shadow-sm me-3 text-center">
                            {!! QrCode::size(64)->generate(route('payments.verify', $payment->reference_number)) !!}
                        </div>
                        <div>
                            <span class="fw-bold text-dark d-block mb-1">Secure QR Verification</span>
                            <p class="text-muted small mb-0">Scan to verify legitimacy via staff app.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-outline-dark px-3 py-2 fw-semibold" style="border-radius: 0.75rem;">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                    <button onclick="window.print()" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" style="border-radius: 0.75rem;">
                        <i class="bi bi-download me-1"></i> PDF
                    </button>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
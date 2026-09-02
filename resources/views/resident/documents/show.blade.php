@extends('layouts.app')

@section('title', 'Document Request Details')

@section('content')
<div class="container py-4">
    <!-- Header & Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Document Request Details</h1>
            <p class="text-muted small mb-0">View the status and information of your requested document.</p>
        </div>
        <div>
            <a href="{{ route('resident.documents.index') }}" class="btn btn-light border fw-semibold text-secondary px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Info Column -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-4">
                    <div>
                        <span class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tracking Number</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold border border-primary border-opacity-10">
                            {{ $documentRequest->tracking_number }}
                        </span>
                    </div>
                    <div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-warning text-dark',
                                'processing' => 'bg-info text-dark',
                                'approved' => 'bg-success text-white',
                                'ready_for_pickup' => 'bg-primary text-white',
                                'completed' => 'bg-success text-white',
                                'rejected' => 'bg-danger text-white',
                            ];
                            $badgeClass = $statusColors[$documentRequest->status] ?? 'bg-secondary text-white';
                        @endphp
                        <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill text-uppercase fw-bold" style="font-size: 0.75rem;">
                            {{ str_replace('_', ' ', $documentRequest->status) }}
                        </span>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Document Type</span>
                        <span class="fw-bold text-dark fs-6">{{ $documentRequest->document_type }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Date Requested</span>
                        <div class="d-flex align-items-center gap-1 text-secondary fw-medium">
                            <i class="bi bi-calendar-event text-primary"></i>
                            <span>{{ $documentRequest->created_at ? $documentRequest->created_at->format('F d, Y h:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Purpose</span>
                    <div class="p-3 bg-light rounded-3 text-secondary small lh-base border">
                        {{ $documentRequest->purpose ?? 'No purpose provided.' }}
                    </div>
                </div>

                @if($documentRequest->admin_remarks)
                    <div class="mb-4">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Admin / Barangay Notes</span>
                        <div class="p-3 bg-warning bg-opacity-10 rounded-3 text-dark small lh-base border border-warning border-opacity-25">
                            {{ $documentRequest->admin_remarks }}
                        </div>
                    </div>
                @endif

                <!-- Payment & Download Section -->
                @php
                    $paymentRecord = \App\Models\Payment::where('payable_type', \App\Models\DocumentRequest::class)
                        ->where('payable_id', $documentRequest->id)
                        ->first();
                    $isPaid = $paymentRecord && $paymentRecord->payment_status === 'paid';
                    $isApproved = in_array($documentRequest->status, ['approved', 'completed', 'ready_for_pickup']);
                @endphp

                <div class="p-3 bg-light rounded-4 border mt-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-check me-1 text-primary"></i> Payment and Document Status</h6>
                    
                    @if($isPaid)
                        <div class="d-flex align-items-center text-success small mb-3">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <span>Your document has been paid (Status: <strong>Paid</strong>).</span>
                        </div>
                    @else
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                            <div class="text-danger small">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> 
                                This document is not yet paid. Please settle your payment before downloading.
                            </div>
                            <a href="{{ route('resident.payments.checkout', ['type' => 'document', 'id' => $documentRequest->id]) }}" class="btn btn-warning btn-sm fw-semibold rounded-pill px-3 shadow-sm">
                                <i class="bi bi-credit-card me-1"></i> Pay Now
                            </a>
                        </div>
                    @endif

                    <!-- Download Button (Appears only if Paid AND Approved/Ready) -->
                    @if($isPaid && $isApproved)
                        <div class="d-grid gap-2">
                            <a href="{{ route('resident.documents.download', $documentRequest->id) }}" class="btn btn-danger fw-semibold rounded-pill py-2 shadow-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF Copy of Document
                            </a>
                        </div>
                    @elseif($isPaid && !$isApproved)
                        <div class="text-muted small fst-italic">
                            <i class="bi bi-info-circle me-1"></i> Your request is currently being reviewed or processed by the admin. The download button will become available once it is approved.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar / Details Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="text-uppercase text-muted fs-7 fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Request Guide</h5>
                <ul class="list-unstyled small text-secondary vstack gap-2 mb-0">
                    <li><i class="bi bi-check2 text-success me-1"></i> <strong>1. Submit:</strong> You request a document.</li>
                    <li><i class="bi bi-check2 text-success me-1"></i> <strong>2. Payment:</strong> Settle the fees online or through available payment options.</li>
                    <li><i class="bi bi-check2 text-success me-1"></i> <strong>3. Approval:</strong> The barangay will review and sign the document.</li>
                    <li><i class="bi bi-check2 text-success me-1"></i> <strong>4. Download:</strong> You can retrieve your PDF copy right here in your portal.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
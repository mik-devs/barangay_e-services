<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification - Barangay E-Portal</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .verification-card { max-width: 600px; margin: 50px auto; border: none; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="card verification-card p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 70px; height: 70px;">
                    <i class="bi bi-shield-fill-check fs-2"></i>
                </div>
                <h3 class="fw-bold text-dark">Barangay Document Verification</h3>
                <p class="text-muted small">Official Online Verification System</p>
            </div>

            @if(isset($documentRequest))
                <div class="alert alert-success text-center mb-4 border-0 bg-success bg-opacity-10 text-success fw-medium">
                    <i class="bi bi-check-circle-fill me-1"></i> This document is <strong>GENUINE</strong> and verified in our system.
                </div>

                <div class="bg-light p-4 rounded-3 mb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Tracking Number</span>
                            <strong class="font-monospace text-dark">{{ $documentRequest->tracking_number }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Document Type</span>
                            <strong class="text-dark">{{ $documentRequest->document_type }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Resident / Owner</span>
                            <strong class="text-dark">{{ $documentRequest->user->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Status</span>
                            <span class="badge bg-{{ $documentRequest->status == 'approved' ? 'success' : 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $documentRequest->status)) }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Pickup / Issued Date</span>
                            <strong class="text-dark">{{ $documentRequest->pickup_date ? $documentRequest->pickup_date->format('M d, Y') : 'N/A' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Fee Paid</span>
                            <strong class="text-dark">₱{{ number_format($documentRequest->fee ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-danger text-center mb-4">
                    <i class="bi bi-x-circle-fill me-1"></i> Document not found or invalid tracking number.
                </div>
            @endif

            <div class="text-center text-muted small">
                &copy; {{ date('Y') }} Barangay E-Portal System. All rights reserved.
            </div>
        </div>
    </div>

</body>
</html>
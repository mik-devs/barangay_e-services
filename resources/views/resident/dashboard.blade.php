@extends('layouts.app')

@section('title', 'Resident Dashboard - Barangay E-Portal')

@section('content')
<div class="container-fluid px-0 py-2">

    <!-- Hero Banner Section -->
    <div class="position-relative p-4 p-lg-5 mb-4 rounded-4 text-white overflow-hidden shadow-sm hero-banner">
        <div class="position-absolute top-0 end-0 translate-middle-y opacity-10 pe-none d-none d-lg-block hero-bg-icon">
            <i class="bi bi-shield-fill-check display-1"></i>
        </div>
        <div class="row align-items-center position-relative z-1">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center bg-white bg-opacity-10 px-3 py-1 rounded-pill mb-3 text-white backdrop-blur badge-custom">
                    <i class="bi bi-patch-check-fill text-info me-2"></i> Verified Resident Portal
                </div>
                <h1 class="fw-bold display-6 mb-2">Welcome back, {{ auth()->user()->first_name ?? 'Resident' }}! 👋</h1>
                <p class="text-white-50 fs-6 mb-4 max-w-600">
                    Access government services, track your document requests, and stay updated with your barangay announcements seamlessly in one secure place.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('resident.documents.create') }}" class="btn btn-light text-primary fw-semibold px-4 py-2.5 rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-plus-fill"></i> Request Clearance
                    </a>
                    <a href="{{ route('resident.announcements.index') }}" class="btn btn-outline-light fw-semibold px-4 py-2.5 rounded-pill d-inline-flex align-items-center gap-2">
                        <i class="bi bi-megaphone"></i> View Announcements
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics & Quick Overview Cards -->
    <div class="row g-4 mb-5">
        <!-- Card 1: Pending Requests -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4 me-3 d-flex align-items-center justify-content-center stat-icon-box">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block text-uppercase fw-bold stat-label">Pending Requests</span>
                        <h3 class="fw-bold mb-0 text-dark mt-1">{{ $pendingCount ?? 0 }}</h3>
                        <span class="text-muted stat-subtext">Awaiting processing</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Portal Status -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-4 me-3 d-flex align-items-center justify-content-center stat-icon-box">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block text-uppercase fw-bold stat-label">Portal Status</span>
                        <h6 class="fw-bold mb-0 text-success mt-1">Active & Online</h6>
                        <span class="text-muted stat-subtext">Secure system connection</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Emergency Help -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-4 me-3 d-flex align-items-center justify-content-center stat-icon-box">
                        <i class="bi bi-telephone-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block text-uppercase fw-bold stat-label">Emergency Help</span>
                        <a href="{{ route('resident.hotlines.directory') }}" class="text-decoration-none fw-bold text-dark d-block mt-1 hover-primary">
                            View Hotlines <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <span class="text-muted stat-subtext">24/7 Barangay Assistance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-grid-fill text-primary me-2"></i> Quick Services</h5>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('resident.documents.create') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 text-center p-4 hover-lift bg-white">
                <div class="card-body">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center quick-icon-box">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Request Document</h6>
                    <p class="text-muted mb-0 quick-subtext">Clearance, Permits, Certificates</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('resident.incidents.create') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 text-center p-4 hover-lift bg-white">
                <div class="card-body">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center quick-icon-box">
                        <i class="bi bi-exclamation-triangle fs-5"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Report Incident</h6>
                    <p class="text-muted mb-0 quick-subtext">Blotter or emergency reports</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('resident.bookings.create') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 text-center p-4 hover-lift bg-white">
                <div class="card-body">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center quick-icon-box">
                        <i class="bi bi-calendar-event fs-5"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Book Facility</h6>
                    <p class="text-muted mb-0 quick-subtext">Basketball court, hall, equipment</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('resident.documents.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 text-center p-4 hover-lift bg-white">
                <div class="card-body">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center quick-icon-box">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Track Status</h6>
                    <p class="text-muted mb-0 quick-subtext">View history of requests</p>
                </div>
            </a>
        </div>
        
        <!-- Naidagdag na Quick Action para sa Payments -->
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('resident.payments.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 text-center p-4 hover-lift bg-white">
                <div class="card-body">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center quick-icon-box">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">My Payments</h6>
                    <p class="text-muted mb-0 quick-subtext">View billing records & receipts</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .hero-banner {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border-radius: 16px !important;
    }
    .hero-bg-icon {
        margin-right: -50px;
        margin-top: 50px;
    }
    .hero-bg-icon i {
        font-size: 15rem;
    }
    .badge-custom {
        font-size: 0.85rem;
        font-weight: 500;
    }
    .max-w-600 {
        max-width: 600px;
    }
    .stat-icon-box {
        width: 60px;
        height: 60px;
    }
    .stat-label {
        font-size: 0.7rem;
        letter-spacing: 0.05em;
    }
    .stat-subtext {
        font-size: 0.75rem;
    }
    .quick-icon-box {
        width: 55px;
        height: 55px;
    }
    .quick-subtext {
        font-size: 0.8rem;
    }
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
    }
    .hover-primary:hover {
        color: #0d6efd !important;
    }
</style>
@endpush
@extends('layouts.admin')

@section('title', 'System Reports & Analytics - Admin Panel')

@section('content')
<div class="container-fluid px-2 px-md-4 py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Reports & Analytics</h1>
            <p class="text-muted small mb-0">Overview of system activities, document requests, incident reports, and facility bookings.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.export') }}" class="btn btn-primary shadow-sm px-4 py-2 rounded-3 small fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-download fs-6"></i> Export Reports
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span class="fw-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- Document Requests -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bi bi-file-earmark-text-fill fs-5"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill small fw-semibold">Total Requests</span>
                </div>
                <h2 class="fw-bold text-dark mb-1 display-6">{{ $documentCount ?? 0 }}</h2>
                <p class="text-muted small mb-0">Document Requests Processed</p>
            </div>
        </div>

        <!-- Incident Reports -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill small fw-semibold">Total Incidents</span>
                </div>
                <h2 class="fw-bold text-dark mb-1 display-6">{{ $incidentCount ?? 0 }}</h2>
                <p class="text-muted small mb-0">Blotter & Incident Reports Logged</p>
            </div>
        </div>

        <!-- Facility Bookings -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bi bi-calendar-check-fill fs-5"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small fw-semibold">Total Bookings</span>
                </div>
                <h2 class="fw-bold text-dark mb-1 display-6">{{ $bookingCount ?? 0 }}</h2>
                <p class="text-muted small mb-0">Facility & Equipment Reservations</p>
            </div>
        </div>
    </div>

    <!-- Analytics Visual Chart Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Module & Community Analytics Overview</h5>
                        <p class="text-muted small mb-0">Visual comparison of system records and registered residents.</p>
                    </div>
                </div>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const analyticsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Document Requests', 'Incident Reports', 'Facility Bookings', 'Registered Residents'],
                datasets: [{
                    label: 'Total Count',
                    data: [
                        {{ $documentCount ?? 0 }}, 
                        {{ $incidentCount ?? 0 }}, 
                        {{ $bookingCount ?? 0 }}, 
                        {{ $residentCount ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(13, 202, 240, 0.7)'
                    ],
                    borderColor: [
                        'rgb(13, 110, 253)',
                        'rgb(255, 193, 7)',
                        'rgb(25, 135, 84)',
                        'rgb(13, 202, 240)'
                    ],
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection
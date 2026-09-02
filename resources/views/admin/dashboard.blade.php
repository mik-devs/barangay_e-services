@extends('layouts.admin')

@section('title', 'Admin Dashboard - Barangay E-Portal')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Command Center Dashboard</h1>
            <p class="text-muted small mb-0">Overview of barangay operations, metrics, and activity logs.</p>
        </div>
    </div>

    <!-- Statistics Widgets -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card p-4 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">TOTAL RESIDENTS</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($totalResidents) }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card p-4 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">PENDING REGISTRATIONS</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($pendingRegistrations) }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-person-plus fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card p-4 border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">PENDING DOCUMENT REQUESTS</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($pendingDocumentRequests) }}</h2>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="card p-4">
                <h5 class="fw-bold mb-3 text-dark">Resident Registration Analytics</h5>
                <canvas id="residentChart" height="90"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities Tables -->
    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3">Recent Document Requests</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tracking #</th>
                                <th>Requester</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDocuments as $doc)
                            <tr>
                                <td class="font-monospace text-primary fw-bold">{{ $doc->tracking_number }}</td>
                                <td>
                                    @if($doc->user)
                                        {{ trim(($doc->user->first_name ?? '') . ' ' . ($doc->user->middle_name ?? '') . ' ' . ($doc->user->last_name ?? '')) ?: $doc->user->name }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $doc->document_type }}</td>
                                <td>
                                    <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : 'warning' }}">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No recent documents found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3">Recent Resident Registrations</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Purok</th>
                                <th>Contact</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentResidents as $res)
                        <tr>
                            <td class="fw-bold text-dark">
                                {{ trim(($res->first_name ?? '') . ' ' . ($res->middle_name ?? '') . ' ' . ($res->last_name ?? '')) ?: ($res->full_name ?? $res->name) }}
                            </td>
                            <td>{{ $res->profile->purok_sitio ?? 'N/A' }}</td>
                            <td>{{ $res->contact_number ?? $res->phone ?? $res->phone_number ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $status = $res->account_status ?? 'pending';
                                @endphp
                                <span class="badge bg-{{ $status == 'verified' ? 'success' : ($status == 'rejected' ? 'danger' : 'warning text-dark') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No recent registrations found.</td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- System Activity Logs Section -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card p-4">
                <h5 class="fw-bold mb-3 text-dark">System Activity Logs</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Admin / User</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activityLogs ?? [] as $log)
                            <tr>
                                <td class="fw-bold text-dark">
                                    {{ $log->user->full_name ?? 'System' }}
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td>{{ $log->description }}</td>
                                <td class="text-muted small">
                                    {{ $log->created_at->format('M d, Y h:i A') }} 
                                    <span class="text-xs">({{ $log->created_at->diffForHumans() }})</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No activity logs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('residentChart').getContext('2d');
    const residentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($registrationMonths) !!},
            datasets: [{
                label: 'New Registrations',
                data: {!! json_encode($registrationCounts) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
</script>
@endpush
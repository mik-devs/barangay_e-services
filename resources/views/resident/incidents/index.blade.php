@extends('layouts.app')

@section('title', 'My Incident Reports - Barangay E-Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">My Incident Reports</h3>
            <p class="text-muted small mb-0">Track the status of your filed complaints or emergency reports.</p>
        </div>
        <a href="{{ route('resident.incidents.create') }}" class="btn btn-danger rounded-pill px-4 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> File New Report
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Report Number</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Location</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td class="fw-bold text-primary">{{ $report->report_number }}</td>
                            <td>{{ $report->incident_type }}</td>
                            <td>{{ $report->location }}</td>
                            <td>{{ \Carbon\Carbon::parse($report->incident_date)->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($report->status == 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
                                @elseif($report->status == 'resolved')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Resolved</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ ucfirst($report->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('resident.incidents.show', $report->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-folder2-open fs-1 d-block mb-2 text-secondary"></i>
                                You haven't filed any incident reports yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
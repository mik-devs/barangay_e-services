@extends('layouts.admin')

@section('title', 'System Activity Logs')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">System Activity Logs</h1>
            <p class="text-muted small mb-0">Monitor system activities, admin actions, and user events across the portal.</p>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="card border-0 shadow-sm rounded-4 p-4" style="min-height: 750px; padding-bottom: 120px !important;">
        <div class="table-responsive overflow-visible">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-3 ps-3 rounded-start-3">Date & Time</th>
                        <th class="py-3">User</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Action / Description</th>
                        <th class="py-3 text-end pe-3 rounded-end-3">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs ?? [] as $log)
                    <tr class="border-bottom border-light">
                        <td class="ps-3 text-muted small">
                            <div class="d-flex align-items-center gap-1 fw-medium text-secondary">
                                <i class="bi bi-calendar3 text-muted"></i>
                                <span>{{ $log->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $log->user->name ?? 'System' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small fw-semibold">
                                {{ ucfirst($log->user->role ?? 'N/A') }}
                            </span>
                        </td>
                        <td>
                            <span class="text-secondary small">{{ $log->description }}</span>
                        </td>
                        <td class="text-end pe-3 font-monospace text-muted small">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-journal-text fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No activity logs recorded</h6>
                                <p class="small text-muted mb-0">There are currently no recorded logs in the system.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-end">
            @if(isset($logs) && method_exists($logs, 'withQueryString'))
                {{ $logs->withQueryString()->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
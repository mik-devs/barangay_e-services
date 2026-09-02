@extends('layouts.admin')

@section('title', 'Resident Management')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Resident Management</h1>
            <p class="text-muted small mb-0">Monitor and manage all registered community residents.</p>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card Container -->
    <div class="card border-0 shadow-sm rounded-4 p-4" style="min-height: 750px; padding-bottom: 120px !important;">
        
        <!-- Search and Status Filter Bar -->
        <form method="GET" action="{{ route('admin.residents.index') }}" class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 shadow-none" placeholder="Search resident name, email, or phone..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-filter"></i></span>
                    <select name="status" class="form-select bg-light border-start-0 shadow-none" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                </div>
            </div>
        </form>

        <div class="table-responsive overflow-visible">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-3 ps-3 rounded-start-3">Resident ID</th>
                        <th class="py-3">Full Name</th>
                        <th class="py-3">Email Address</th>
                        <th class="py-3">Phone Number</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Date Registered</th>
                        <th class="py-3 text-end pe-3 rounded-end-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($residents as $resident)
                    <tr class="border-bottom border-light">
                        <td class="ps-3 font-monospace text-primary fw-bold">#{{ $resident->id }}</td>
                        <td>
                            <div class="fw-semibold text-dark">
                                {{ trim(($resident->first_name ?? '') . ' ' . ($resident->middle_name ?? '') . ' ' . ($resident->last_name ?? '')) ?: $resident->name }}
                            </div>
                        </td>
                        <td>
                            <span class="text-secondary fw-medium small">{{ $resident->email }}</span>
                        </td>
                        <td>
                            <span class="text-dark fw-medium small">{{ $resident->phone_number ?? '-' }}</span>
                        </td>
                        <td>
                            @php
                                $status = strtolower($resident->account_status ?? 'active');
                                $badgeBg = 'bg-secondary text-white';
                                $dotColor = 'bg-white';
                                
                                if ($status == 'active' || $status == 'verified') {
                                    $badgeBg = 'bg-success text-white';
                                    $dotColor = 'bg-white';
                                } elseif ($status == 'pending') {
                                    $badgeBg = 'bg-warning text-dark';
                                    $dotColor = 'bg-dark';
                                } elseif ($status == 'inactive') {
                                    $badgeBg = 'bg-danger text-white';
                                    $dotColor = 'bg-white';
                                }
                            @endphp
                            <span class="badge {{ $badgeBg }} rounded-pill px-3 py-2 fw-semibold text-capitalize d-inline-flex align-items-center shadow-sm">
                                <span class="spinner-grow spinner-grow-sm me-1 {{ $dotColor }}" style="width: 6px; height: 6px;" role="status"></span>
                                {{ ucfirst($resident->account_status ?? 'Active') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-secondary small fw-medium">
                                <i class="bi bi-calendar3 text-muted"></i>
                                <span>{{ $resident->created_at ? $resident->created_at->format('M d, Y') : '-' }}</span>
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-circle shadow-sm p-1" style="width: 32px; height: 32px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2">
                                    <li>
                                        <a class="dropdown-item py-2 px-3 text-dark small fw-medium" href="{{ route('admin.residents.show', $resident->id) }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> View Profile
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-people fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No residents found</h6>
                                <p class="small text-muted mb-0">There are currently no registered residents matching your filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-end">
            {{ $residents->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
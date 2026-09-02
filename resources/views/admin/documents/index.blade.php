@extends('layouts.admin')

@section('title', 'Document Requests Management')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Document Requests Management</h1>
            <p class="text-muted small mb-0">Monitor and manage all resident document certification requests.</p>
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
        
        <!-- Search and Filter Bar -->
        <form method="GET" action="{{ route('admin.documents.index') }}" class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 shadow-none" placeholder="Search resident, document type..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
    <div class="input-group">
        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-filter"></i></span>
        <select name="status" class="form-select bg-light border-start-0 shadow-none" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="ready_for_pickup" {{ request('status') == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>
</div>
        </form>

        <div class="table-responsive overflow-visible">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-3 ps-3 rounded-start-3">Request ID</th>
                        <th class="py-3">Resident</th>
                        <th class="py-3">Document Type</th>
                        <th class="py-3">Purpose</th>
                        <th class="py-3">Date Requested</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-3 rounded-end-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                    <tr class="border-bottom border-light">
                        <td class="ps-3 font-monospace text-primary fw-bold">#{{ $document->id }}</td>
                        <td>
                            <div class="fw-semibold text-dark">
                                @if($document->user)
                                    {{ trim(($document->user->first_name ?? '') . ' ' . ($document->user->middle_name ?? '') . ' ' . ($document->user->last_name ?? '')) ?: $document->user->name }}
                                @else
                                    <span class="text-muted fw-normal">N/A</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ $document->document_type ?? $document->type ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="text-muted small" title="{{ $document->purpose }}">
                                {{ Str::limit($document->purpose, 30) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-secondary small fw-medium">
                                <i class="bi bi-calendar3 text-muted"></i>
                                <span>{{ $document->created_at ? $document->created_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $status = strtolower($document->status);
                                $badgeBg = 'bg-secondary text-white';
                                $dotColor = 'bg-white';
                                
                                if ($status == 'pending') {
                                    $badgeBg = 'bg-warning text-dark';
                                    $dotColor = 'bg-dark';
                                } elseif ($status == 'approved' || $status == 'verified' || $status == 'ready' || $status == 'for_payment') {
                                    $badgeBg = 'bg-success text-white';
                                    $dotColor = 'bg-white';
                                } elseif ($status == 'rejected' || $status == 'cancelled') {
                                    $badgeBg = 'bg-danger text-white';
                                    $dotColor = 'bg-white';
                                }
                            @endphp
                            <span class="badge {{ $badgeBg }} rounded-pill px-3 py-2 fw-semibold text-capitalize d-inline-flex align-items-center shadow-sm">
                                <span class="spinner-grow spinner-grow-sm me-1 {{ $dotColor }}" style="width: 6px; height: 6px;" role="status"></span>
                                {{ ucfirst($document->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-circle shadow-sm p-1" style="width: 32px; height: 32px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2">
                                    <li>
                                        <a class="dropdown-item py-2 px-3 text-dark small fw-medium" href="{{ route('admin.documents.show', $document->id) }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> View Details
                                        </a>
                                    </li>
                                    @if($document->status == 'pending')
                                    <li>
                                        <!-- Trigger Modal to set fee and approve -->
                                        <button type="button" class="dropdown-item py-2 px-3 text-success small fw-medium" data-bs-toggle="modal" data-bs-target="#approveModal{{ $document->id }}">
                                            <i class="bi bi-check-circle me-2"></i> Approve & Set Fee
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>

                    <!-- APPROVAL MODAL WITH FEE INPUT -->
                    <div class="modal fade" id="approveModal{{ $document->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow rounded-4 text-start">
                                <form action="{{ route('admin.documents.approve', $document->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                                        <h5 class="fw-bold text-dark">Approve Request & Set Fee</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-4 py-3">
                                        <p class="text-muted small mb-3">
                                            Set the fee for this document so it automatically appears in the resident's **My Payments** tab.
                                        </p>
                                        <div class="mb-3">
                                            <label for="fee{{ $document->id }}" class="form-label fw-semibold small">Document Fee (PHP)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">₱</span>
                                                <input type="number" step="0.01" min="0" class="form-control" id="fee{{ $document->id }}" name="fee" placeholder="0.00" required value="{{ $document->fee ?? 50.00 }}">
                                            </div>
                                            <div class="form-text text-muted" style="font-size: 0.75rem;">Enter 0 if the document is free.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 px-4 pb-4">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Approve</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-file-earmark-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No document requests found</h6>
                                <p class="small text-muted mb-0">There are currently no document requests matching your filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-end">
            {{ $documents->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'My Request History - Barangay E-Portal')

@section('content')
<div class="container py-4">
    
    <!-- Header with Action Button -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-clock-history text-primary me-2"></i>My Request History</h4>
            <p class="text-muted small mb-0">Track your submitted document requests and claim status.</p>
        </div>
        <a href="{{ route('resident.documents.create') }}" class="btn btn-primary rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> New Request
        </a>
    </div>

    <!-- Alert Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- FILTER TABS -->
    @php
        $currentType = request('type', 'all');
    @endphp
    <div class="mb-3 overflow-auto">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $currentType == 'all' ? 'active shadow-sm' : 'bg-light text-dark' }}" 
                   href="{{ route('resident.documents.index', ['type' => 'all']) }}">
                   All
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $currentType == 'Certificate of Clearance' ? 'active shadow-sm' : 'bg-light text-dark' }}" 
                   href="{{ route('resident.documents.index', ['type' => 'Certificate of Clearance']) }}">
                   Clearance
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $currentType == 'Barangay Business Permit' ? 'active shadow-sm' : 'bg-light text-dark' }}" 
                   href="{{ route('resident.documents.index', ['type' => 'Barangay Business Permit']) }}">
                   Business Permit
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $currentType == 'Certificate of Indigency' ? 'active shadow-sm' : 'bg-light text-dark' }}" 
                   href="{{ route('resident.documents.index', ['type' => 'Certificate of Indigency']) }}">
                   Indigency
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $currentType == 'Certificate of Residency' ? 'active shadow-sm' : 'bg-light text-dark' }}" 
                   href="{{ route('resident.documents.index', ['type' => 'Certificate of Residency']) }}">
                   Residency
                </a>
            </li>
        </ul>
    </div>

    <!-- Requests Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Tracking No.</th>
                            <th class="py-3">Document Type</th>
                            <th class="py-3">Purpose</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Date Requested</th>
                            <th class="pe-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $item)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">
                                    <code>{{ $item->tracking_number }}</code>
                                </td>
                                <td class="fw-semibold text-dark">{{ $item->document_type }}</td>
                                <td class="text-muted">{{ Str::limit($item->purpose, 30) }}</td>
                                <td>
                                    @if($item->status == 'pending')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                    @elseif($item->status == 'processing')
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-2"><i class="bi bi-gear-wide-connected me-1"></i> Processing</span>
                                    @elseif($item->status == 'approved' || $item->status == 'ready_for_pickup' || $item->status == 'completed')
                                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> Approved</span>
                                    @elseif($item->status == 'rejected')
                                        <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i> Rejected</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($item->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $item->created_at->format('M d, Y h:i A') }}</td>
                                <td class="pe-4 text-end">
                                    @php
                                        $payment = \App\Models\Payment::where('payable_type', \App\Models\DocumentRequest::class)
                                            ->where('payable_id', $item->id)
                                            ->first();
                                    @endphp

                                    @if($payment && $payment->payment_status === 'paid')
                                        <a href="{{ route('resident.documents.download', $item->id) }}" class="btn btn-sm btn-danger rounded-pill px-2 mb-1">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('resident.documents.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 mb-1">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    No document requests found yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($requests->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $requests->appends(['type' => $currentType])->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
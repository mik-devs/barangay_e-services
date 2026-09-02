@extends('layouts.admin') {{-- Preserving existing admin layout --}}

@section('title', 'Payment Command Center & Revenue Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1 tracking-tight">Payment & Revenue Dashboard</h2>
            <p class="text-muted small mb-0">Monitor online transactions, collections, and financial reports in real-time.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-dark px-3 py-2 fw-semibold shadow-sm" style="border-radius: 0.75rem;">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Export Reports
            </a>
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

    <!-- Statistics Cards Grid -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 1rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Today's Revenue</span>
                        <h3 class="fw-bold text-dark mb-0">₱{{ number_format($todayRevenue ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-4 shadow-sm">
                        <i class="bi bi-currency-peso fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 1rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Weekly Collection</span>
                        <h3 class="fw-bold text-dark mb-0">₱{{ number_format($weeklyRevenue ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-4 shadow-sm">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 1rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Monthly Revenue</span>
                        <h3 class="fw-bold text-dark mb-0">₱{{ number_format($monthlyRevenue ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-4 shadow-sm">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Count Metrics Sub-row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 1rem;">
                <span class="text-muted small">Pending Payments</span>
                <h4 class="fw-bold text-warning mt-1 mb-0">{{ $pendingPayments ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 1rem;">
                <span class="text-muted small">Completed Paid</span>
                <h4 class="fw-bold text-success mt-1 mb-0">{{ $completedPayments ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 1rem;">
                <span class="text-muted small">Failed Transactions</span>
                <h4 class="fw-bold text-danger mt-1 mb-0">{{ $failedTransactions ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white text-center" style="border-radius: 1rem;">
                <span class="text-muted small">Refund Requests</span>
                <h4 class="fw-bold text-secondary mt-1 mb-0">{{ $refundRequests ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table & Filter Bar -->
    <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 1rem;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h5 class="fw-bold text-dark mb-0">Payment Transactions</h5>
            
            <!-- Search and Status Filter Form -->
            <form method="GET" action="{{ route('admin.payments.dashboard') }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 shadow-none" placeholder="Search ref, OR, resident..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm bg-light shadow-none" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light text-uppercase fs-7 text-secondary" style="font-size: 0.75rem;">
                    <tr>
                        <th class="py-3 ps-3">Reference # / OR #</th>
                        <th class="py-3">Resident</th>
                        <th class="py-3">Service Type</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">Method</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments ?? [] as $payment)
                    <tr>
                        <td class="ps-3 font-monospace fw-bold text-dark">
                            {{ $payment->reference_number }}
                            @if($payment->or_number)
                                <br><small class="text-muted fw-normal">OR: {{ $payment->or_number }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">
                                {{ optional($payment->resident)->first_name ?? '' }} {{ optional($payment->resident)->last_name ?? optional($payment->resident)->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td>{{ class_basename($payment->payable_type) }}</td>
                        <td class="fw-bold">₱{{ number_format($payment->amount, 2) }}</td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ $payment->payment_method }}</span></td>
                        <td>
                            @php
                                $statusColor = match($payment->payment_status) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'refunded' => 'secondary',
                                    default => 'light text-dark'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border px-2 py-1 rounded-pill">
                                {{ ucfirst($payment->payment_status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-circle shadow-sm p-1" style="width: 32px; height: 32px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2">
                                    <li>
                                        <a class="dropdown-item py-2 px-3 text-dark small fw-medium" href="{{ route('payments.verify', $payment->reference_number) }}" target="_blank">
                                            <i class="bi bi-eye me-2 text-primary"></i> View Receipt
                                        </a>
                                    </li>
                                    @if($payment->payment_status === 'pending')
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('admin.payments.verify-offline', $payment->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item py-2 px-3 text-success small fw-medium">
                                                    <i class="bi bi-check-circle me-2"></i> Verify Offline Payment
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    @if($payment->payment_status === 'paid')
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button" class="dropdown-item py-2 px-3 text-danger small fw-medium" data-bs-toggle="modal" data-bs-target="#refundModal{{ $payment->id }}">
                                                <i class="bi bi-arrow-counterclockwise me-2"></i> Process Refund
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Refund Modal per payment -->
                            @if($payment->payment_status === 'paid')
                            <div class="modal fade text-start" id="refundModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Process Refund</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-muted mb-3">Ref#: <strong>{{ $payment->reference_number }}</strong> | Amount: <strong>₱{{ number_format($payment->amount, 2) }}</strong></p>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Reason for Refund</label>
                                                    <textarea name="reason" class="form-control" rows="3" required placeholder="Enter valid reason..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">Confirm Refund</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No transaction records found matching your filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-end">
            {{ $recentPayments->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
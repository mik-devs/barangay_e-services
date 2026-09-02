@extends('layouts.admin')

@section('title', 'Manage Hotlines')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Emergency Hotlines</h1>
            <p class="text-muted small mb-0">Manage emergency contact numbers and department hotlines for residents.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary px-3 py-2 rounded-3 small fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addHotlineModal">
                <i class="bi bi-plus-lg me-1"></i> Add New Hotline
            </button>
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
        <div class="table-responsive overflow-visible">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-3 ps-3 rounded-start-3">Agency Name / Department</th>
                        <th class="py-3">Contact Number</th>
                        <th class="py-3">Description</th>
                        <th class="py-3 text-end pe-3 rounded-end-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hotlines ?? [] as $hotline)
                    <tr class="border-bottom border-light">
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                    <i class="bi bi-telephone-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $hotline->agency_name }}</div>
                                    <div class="text-muted small">{{ $hotline->sub_text ?? 'Emergency Contact' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $hotline->contact_number }}</span>
                        </td>
                        <td>
                            <span class="text-secondary small">{{ $hotline->description ?? 'Available 24/7 anytime' }}</span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-inline-flex gap-1">
                                <!-- Edit Button triggers modal -->
                                <button type="button" class="btn btn-sm btn-light border rounded-circle shadow-sm p-1 text-warning" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#editHotlineModal{{ $hotline->id }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete Form -->
                                <form action="{{ route('admin.hotlines.destroy', $hotline->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this hotline?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-circle shadow-sm p-1 text-danger" style="width: 32px; height: 32px;" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Hotline Modal for each item -->
                    <div class="modal fade" id="editHotlineModal{{ $hotline->id }}" tabindex="-1" aria-labelledby="editHotlineModalLabel{{ $hotline->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4 p-3 bg-white text-start">
                                <div class="modal-header border-0 pb-1">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title fw-bold text-dark mb-0" id="editHotlineModalLabel{{ $hotline->id }}">Edit Hotline</h5>
                                            <p class="text-muted small mb-0">Update emergency contact or department details.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <form action="{{ route('admin.hotlines.update', $hotline->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body py-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Agency Name / Department</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-3"><i class="bi bi-building"></i></span>
                                                <input type="text" class="form-control bg-light border-start-0 ps-0 shadow-none py-2 rounded-end-3" name="agency_name" value="{{ $hotline->agency_name }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Contact Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-3"><i class="bi bi-telephone"></i></span>
                                                <input type="text" class="form-control bg-light border-start-0 ps-0 shadow-none py-2 rounded-end-3" name="contact_number" value="{{ $hotline->contact_number }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label small fw-semibold text-dark">Description</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-3"><i class="bi bi-info-circle"></i></span>
                                                <input type="text" class="form-control bg-light border-start-0 ps-0 shadow-none py-2 rounded-end-3" name="description" value="{{ $hotline->description }}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer border-0 pt-0 pb-2">
                                        <button type="button" class="btn btn-light rounded-3 px-3 py-2 small fw-semibold text-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 small fw-semibold shadow-sm">
                                            <i class="bi bi-check-lg me-1"></i> Update Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-telephone-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No hotlines found</h6>
                                <p class="small text-muted mb-0">No emergency hotline numbers have been added yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-end">
            @if(isset($hotlines) && method_exists($hotlines, 'withQueryString'))
                {{ $hotlines->withQueryString()->links() }}
            @endif
        </div>
    </div>
</div>

<!-- Modern Add Hotline Modal -->
<div class="modal fade" id="addHotlineModal" tabindex="-1" aria-labelledby="addHotlineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 p-3 bg-white">
            <div class="modal-header border-0 pb-1">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-telephone-plus-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="addHotlineModalLabel">Add New Hotline</h5>
                        <p class="text-muted small mb-0">Register an emergency contact or department.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('admin.hotlines.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="agency_name" class="form-label small fw-semibold text-dark">Agency Name / Department</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-3"><i class="bi bi-building"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 ps-0 shadow-none py-2 rounded-end-3" id="agency_name" name="agency_name" placeholder="e.g. Barangay Police" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label small fw-semibold text-dark">Contact Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-3"><i class="bi bi-telephone"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 ps-0 shadow-none py-2 rounded-end-3" id="contact_number" name="contact_number" placeholder="e.g. 0956256254" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label for="description" class="form-label small fw-semibold text-dark">Description</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-3"><i class="bi bi-info-circle"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 ps-0 shadow-none py-2 rounded-end-3" id="description" name="description" placeholder="e.g. Available 24/7 anytime">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0 pb-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-2 small fw-semibold text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 small fw-semibold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Save Hotline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
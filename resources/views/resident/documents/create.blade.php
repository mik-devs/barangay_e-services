@extends('layouts.app')

@section('title', 'Request Document - Barangay E-Portal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Request Document</li>
                </ol>
            </nav>

            <!-- Card Form Container -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white p-4 rounded-top-4 border-0">
                    <h5 class="fw-bold mb-1"><i class="bi bi-file-earmark-plus me-2"></i>Online Document Request</h5>
                    <p class="mb-0 text-white-50 small">Fill in the required information to request official barangay documents.</p>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('resident.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Document Type Selection -->
                        <div class="mb-3">
                            <label for="document_type" class="form-label fw-semibold">Document Type <span class="text-danger">*</span></label>
                            <select name="document_type" id="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                                <option value="" selected disabled>-- Select Document --</option>
                                <option value="Barangay Clearance">Barangay Clearance</option>
                                <option value="Certificate of Indigency">Certificate of Indigency</option>
                                <option value="Certificate of Residency">Certificate of Residency</option>
                                <option value="Business Permit Clearance">Business Permit Clearance</option>
                            </select>
                            @error('document_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Purpose -->
                        <div class="mb-3">
                            <label for="purpose" class="form-label fw-semibold">Purpose <span class="text-danger">*</span></label>
                            <input type="text" name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror" placeholder="e.g., Job Application, Scholarship, Postal ID" value="{{ old('purpose') }}" required>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Additional Remarks -->
                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-semibold">Additional Remarks / Notes</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror" placeholder="Any specific instructions or details...">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Supporting Document / ID Attachment -->
                        <div class="mb-4">
                            <label for="id_attachment" class="form-label fw-semibold">Attach Valid ID / Supporting Document</label>
                            <input type="file" name="id_attachment" id="id_attachment" class="form-control @error('id_attachment') is-invalid @enderror">
                            <div class="form-text">Accepted formats: JPG, PNG, PDF (Max size: 2MB).</div>
                            @error('id_attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                Submit Request <i class="bi bi-send ms-1"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
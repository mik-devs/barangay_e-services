@extends('layouts.admin')

@section('title', 'Document Request Details')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Document Request Details</h1>
            <p class="text-muted small mb-0">Review request info, update status, and generate official documents.</p>
        </div>
        <div>
            <a href="{{ route('admin.documents.index') }}" class="btn btn-light border fw-semibold text-secondary px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Details Column -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-4">
                    <div>
                        <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tracking Number</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold border border-primary border-opacity-10">
                            {{ $documentRequest->tracking_number }}
                        </span>
                    </div>

                    <!-- Document Generation Action Buttons -->
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.documents.pdf', $documentRequest->id) }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold shadow-sm" title="View or Print PDF">
                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                        </a>
                        <a href="{{ route('admin.documents.download-word', $documentRequest->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm" title="Download Word Document">
                            <i class="bi bi-file-earmark-word me-1"></i> Word (.doc)
                        </a>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Document Type</span>
                        <span class="fw-bold text-dark fs-6">{{ $documentRequest->document_type }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Date Requested</span>
                        <div class="d-flex align-items-center gap-1 text-secondary fw-medium">
                            <i class="bi bi-calendar-event text-primary"></i>
                            <span>{{ $documentRequest->created_at ? $documentRequest->created_at->format('F d, Y h:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Purpose</span>
                    <div class="p-3 bg-light rounded-3 text-secondary small lh-base border">
                        {{ $documentRequest->purpose ?? 'No purpose provided.' }}
                    </div>
                </div>

                <!-- Resident Additional Remarks / Notes -->
                <div class="mb-4">
                    <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Resident Remarks / Notes</span>
                    <div class="p-3 bg-light rounded-3 text-secondary small lh-base border">
                        {{ $documentRequest->remarks ?? 'No additional remarks provided.' }}
                    </div>
                </div>

                <!-- Supporting Attachment / ID Preview Section -->
                <div class="mb-4">
                    <span class="d-block text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.7rem;">Supporting Proof / ID Attachment</span>
                    
                    @if(!empty($documentRequest->attachment))
                        <div class="p-3 bg-light rounded-3 border text-center">
                            @php
                                $filePath = asset('storage/' . $documentRequest->attachment);
                                $extension = pathinfo($documentRequest->attachment, PATHINFO_EXTENSION);
                            @endphp

                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp']))
                                <div class="mb-2">
                                    <img src="{{ $filePath }}" alt="ID Attachment" class="img-fluid rounded border shadow-sm" style="max-height: 300px; object-fit: contain;">
                                </div>
                                <a href="{{ $filePath }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-arrows-fullscreen me-1"></i> View Full Size Image
                                </a>
                            @else
                                <div class="text-secondary small mb-2">
                                    <i class="bi bi-file-earmark-text fs-3 text-warning"></i>
                                    <span class="d-block">Attached Document ({{ strtoupper($extension) }})</span>
                                </div>
                                <a href="{{ $filePath }}" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-download me-1"></i> Download / View File
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="p-3 bg-light rounded-3 text-secondary small border text-center text-muted">
                            No ID attachment uploaded by the resident.
                        </div>
                    @endif
                </div>

                @if($documentRequest->status === 'approved' || $documentRequest->status === 'completed' || $documentRequest->status === 'ready_for_pickup')
                    <div class="pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.documents.download-word', $documentRequest->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-file-earmark-word me-1"></i> Download Word Document
                        </a>
                        <a href="{{ route('admin.documents.pdf', $documentRequest->id) }}" target="_blank" class="btn btn-danger btn-sm rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Print / Download PDF Document
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar / Resident Info & Status Update -->
        <div class="col-lg-4">
            <!-- Resident Info Card -->
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <h5 class="text-uppercase text-muted fs-7 fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Resident Details</h5>
                
                @php 
                    $resident = $documentRequest->user; 
                    $profile = $resident ? $resident->residentProfile : null;
                @endphp

                @if($resident)
                    <div class="vstack gap-3 small">
                        <!-- Full Name -->
                        <div>
                            <span class="d-block text-muted" style="font-size: 0.7rem;">Full Name</span>
                            <span class="fw-bold text-dark">
                                @php
                                    $rawName = trim(($resident->first_name ?? '') . ' ' . ($resident->middle_name ?? '') . ' ' . ($resident->last_name ?? '') . ' ' . ($resident->suffix ?? ''));
                                    $cleanName = trim(str_ireplace('N/A', '', $rawName));
                                @endphp
                                {{ $cleanName ?: ($resident->name ?? 'N/A') }}
                            </span>
                        </div>

                        <!-- Contact Info -->
                        <div class="row g-2">
                            <div class="col-6">
                                <span class="d-block text-muted" style="font-size: 0.7rem;">Email Address</span>
                                <span class="text-secondary text-break">{{ $resident->email }}</span>
                            </div>
                            <div class="col-6">
                                <span class="d-block text-muted" style="font-size: 0.7rem;">Phone Number</span>
                                <span class="text-secondary">{{ $resident->phone_number ?? 'N/A' }}</span>
                            </div>
                        </div>

                        @if($profile)
                            <!-- Demographics -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Birth Date</span>
                                    <span class="text-secondary">{{ $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->format('M d, Y') : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Gender</span>
                                    <span class="text-secondary">{{ $profile->gender ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Civil Status</span>
                                    <span class="text-secondary">{{ $profile->civil_status ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Citizenship / Occupation</span>
                                    <span class="text-secondary">{{ $profile->citizenship ?? 'N/A' }} / {{ $profile->occupation ?? 'None' }}</span>
                                </div>
                            </div>

                            <!-- Birth Place -->
                            <div>
                                <span class="d-block text-muted" style="font-size: 0.7rem;">Birth Place</span>
                                <span class="text-secondary">{{ $profile->birth_place ?? 'N/A' }}</span>
                            </div>

                            <!-- Address -->
                            <div>
                                <span class="d-block text-muted" style="font-size: 0.7rem;">Address (House #, Street, Purok)</span>
                                <span class="text-secondary fw-medium">
                                    @php
                                        $houseNo = trim($profile->house_number ?? '');
                                        $street = 'Mayas St.';
                                        $purokRaw = trim($profile->purok_sitio ?? '');
                                        $purokClean = trim(preg_replace('/^purok\s*/i', '', $purokRaw));
                                        
                                        $fullAddress = collect([
                                            $houseNo ? $houseNo : null,
                                            $street,
                                            $purokClean ? 'Purok ' . $purokClean : null
                                        ])->filter()->implode(', ');
                                    @endphp
                                    {{ $fullAddress ?: 'N/A' }}
                                </span>
                            </div>

                            <!-- Voter Status -->
                            <div>
                                <span class="d-block text-muted" style="font-size: 0.7rem;">Voter Status</span>
                                <span class="text-secondary">
                                    {{ $profile->is_voter ? 'Registered (Precinct: ' . ($profile->voter_precinct_no ?? 'N/A') . ')' : 'Not a Voter' }}
                                </span>
                            </div>

                            @if($profile->id_front_path || $profile->proof_of_residency_path)
                                <div class="pt-2 border-top mt-2">
                                    <span class="d-block text-muted mb-1" style="font-size: 0.7rem;">Registered ID Type: <strong>{{ $profile->id_type ?? 'N/A' }}</strong></span>
                                    <span class="d-block text-muted mb-2" style="font-size: 0.7rem;">ID Number: <strong>{{ $profile->id_number ?? 'N/A' }}</strong></span>
                                </div>
                            @endif
                        @else
                            <div class="p-2 bg-light rounded text-muted small">
                                No extended resident profile linked.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-3 bg-light rounded-3 text-center border">
                        <span class="text-muted small fw-medium">No resident record attached.</span>
                    </div>
                @endif
            </div>

            <!-- Update Status & Fee Card -->
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <h5 class="text-uppercase text-muted fs-7 fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Update Status & Fee</h5>
                
                <form action="{{ route('admin.documents.status', $documentRequest->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="status" class="form-label small fw-bold text-dark">Current Status</label>
                        <select name="status" id="status" class="form-select form-select-sm bg-light">
                            <option value="pending" {{ $documentRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $documentRequest->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="approved" {{ $documentRequest->status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="ready_for_pickup" {{ $documentRequest->status == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                            <option value="completed" {{ $documentRequest->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="rejected" {{ $documentRequest->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <!-- Document Fee Input Field -->
                    <div class="mb-3">
                        <label for="fee" class="form-label small fw-bold text-dark">Document Fee (₱)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">₱</span>
                            <input type="number" step="0.01" name="fee" id="fee" value="{{ old('fee', $documentRequest->fee ?? 0.00) }}" class="form-control bg-light" placeholder="0.00">
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;">Set the fee amount to be paid for this document.</small>
                    </div>

                    <div class="mb-3">
                        <label for="pickup_date" class="form-label small fw-bold text-dark">Pickup Date</label>
                        <input type="date" name="pickup_date" id="pickup_date" value="{{ old('pickup_date', $documentRequest->pickup_date ? \Carbon\Carbon::parse($documentRequest->pickup_date)->format('Y-m-d') : '') }}" class="form-control form-control-sm bg-light">
                    </div>

                    <div class="mb-3">
                        <label for="admin_remarks" class="form-label small fw-bold text-dark">Admin Remarks / Notes</label>
                        <textarea name="admin_remarks" id="admin_remarks" rows="3" class="form-control form-control-sm bg-light" placeholder="Optional notes...">{{ $documentRequest->admin_remarks }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold py-2 rounded-pill shadow-sm">
                        <i class="bi bi-save me-1"></i> Update Request
                    </button>
                </form>
            </div>

            <!-- Upload Signed / Final Document Card (Auto-Signed by FPDI) -->
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="text-uppercase text-muted fs-7 fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Upload Signed / Final Document</h5>
                
                <form action="{{ route('admin.documents.uploadAndSign', $documentRequest->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="admin_pdf" class="form-label small fw-bold text-dark">Choose PDF File (Automatically embeds signature)</label>
                        <input type="file" name="admin_pdf" id="admin_pdf" class="form-control form-control-sm bg-light" accept=".pdf" required>
                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Max size: 10MB. System will automatically embed Punong Barangay signature.</small>
                    </div>

                    <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold py-2 rounded-pill shadow-sm">
                        <i class="bi bi-upload me-1"></i> Upload & Auto-Sign
                    </button>
                </form>

                @if($documentRequest->completed_document)
                    <div class="mt-3 pt-3 border-top text-center">
                        <span class="d-block text-muted small mb-2" style="font-size: 0.75rem;">Current Signed File:</span>
                        <a href="{{ asset('storage/' . $documentRequest->completed_document) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i> View Signed PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
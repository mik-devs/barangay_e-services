@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Report Incident</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white p-4 border-0">
                    <h5 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Report an Incident</h5>
                    <p class="mb-0 text-white-50 small">Provide accurate details to help barangay officials respond quickly.</p>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('resident.incidents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="incident_type" class="form-label fw-semibold">Incident Type <span class="text-danger">*</span></label>
                                <select name="incident_type" id="incident_type" class="form-select @error('incident_type') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Select Incident Type --</option>
                                    <option value="Noise Disturbance">Noise Disturbance</option>
                                    <option value="Physical Altercation">Physical Altercation / Brawl</option>
                                    <option value="Theft / Robbery">Theft / Robbery</option>
                                    <option value="Vehicular Accident">Vehicular Accident</option>
                                    <option value="Public Hazard">Public Hazard / Safety Issue</option>
                                    <option value="Other Concern">Other Emergency / Concern</option>
                                </select>
                                @error('incident_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="incident_date" class="form-label fw-semibold">Date & Time Occurred <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="incident_date" id="incident_date" class="form-control @error('incident_date') is-invalid @enderror" required>
                                @error('incident_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label fw-semibold">Exact Location / Barangay Area <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" placeholder="e.g., Purok 3, Near Elementary School" required>
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Detailed Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Describe what happened, individuals involved, etc." required></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="attachment" class="form-label fw-semibold">Upload Photo Evidence (Optional)</label>
                            <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="image/*">
                            <div class="form-text">Accepted image formats: JPG, PNG (Max: 5MB).</div>
                            @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                                Submit Incident Report <i class="bi bi-send ms-1"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
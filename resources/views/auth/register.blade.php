@extends('layouts.guest')

@section('title', 'Resident Portal Registration')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-modern shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-vcard fs-2"></i>
                        </div>
                        <h3 class="fw-bold">Barangay Resident Registration</h3>
                        <p class="text-muted">Fill out your information to create an official barangay portal account.</p>
                    </div>

                    <!-- Step Progress Indicator -->
                    <div class="position-relative mb-5">
                        <div class="progress" style="height: 3px;">
                            <div class="progress-bar bg-primary transition-all" id="stepProgress" role="progressbar" style="width: 25%;"></div>
                        </div>
                        <div class="d-flex justify-content-between position-relative mt-n3">
                            <button type="button" class="btn btn-sm btn-primary rounded-circle" id="stepIndicator1" onclick="navigateToStep(1)">1</button>
                            <button type="button" class="btn btn-sm btn-secondary rounded-circle" id="stepIndicator2" onclick="navigateToStep(2)">2</button>
                            <button type="button" class="btn btn-sm btn-secondary rounded-circle" id="stepIndicator3" onclick="navigateToStep(3)">3</button>
                            <button type="button" class="btn btn-sm btn-secondary rounded-circle" id="stepIndicator4" onclick="navigateToStep(4)">4</button>
                        </div>
                    </div>

                    <!-- Global Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following errors:</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" id="registrationForm">
                        @csrf

                        <!-- STEP 1: Basic Account Info -->
                        <div class="step-section" id="step1">
                            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Step 1: Basic Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror" value="{{ old('middle_name') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Suffix</label>
                                    <input type="text" name="suffix" class="form-control @error('suffix') is-invalid @enderror" placeholder="e.g. Jr." value="{{ old('suffix') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" placeholder="09171234567" value="{{ old('phone_number') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary px-4" onclick="nextStep(2)">Next: Residence Details <i class="bi bi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        <!-- STEP 2: Resident Profile & Address -->
                        <div class="step-section d-none" id="step2">
                            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-geo-alt-fill me-2"></i>Step 2: Resident Profile & Address</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Place of Birth <span class="text-danger">*</span></label>
                                    <input type="text" name="birth_place" class="form-control @error('birth_place') is-invalid @enderror" value="{{ old('birth_place') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Sex <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Civil Status <span class="text-danger">*</span></label>
                                    <select name="civil_status" class="form-select @error('civil_status') is-invalid @enderror" required>
                                        <option value="Single" {{ old('civil_status', 'Single') == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Citizenship <span class="text-danger">*</span></label>
                                    <input type="text" name="citizenship" class="form-control @error('citizenship') is-invalid @enderror" value="{{ old('citizenship', 'Filipino') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Occupation</label>
                                    <input type="text" name="occupation" class="form-control @error('occupation') is-invalid @enderror" value="{{ old('occupation') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">House No. <span class="text-danger">*</span></label>
                                    <input type="text" name="house_number" class="form-control @error('house_number') is-invalid @enderror" value="{{ old('house_number') }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Street <span class="text-danger">*</span></label>
                                    <input type="text" name="street" class="form-control @error('street') is-invalid @enderror" value="{{ old('street') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Purok/Sitio <span class="text-danger">*</span></label>
                                    <input type="text" name="purok_sitio" class="form-control @error('purok_sitio') is-invalid @enderror" value="{{ old('purok_sitio') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Registered Voter in Barangay? <span class="text-danger">*</span></label>
                                    <select name="is_voter" class="form-select @error('is_voter') is-invalid @enderror" id="voterSelect" onchange="togglePrecinct(this.value)">
                                        <option value="1" {{ old('is_voter', '1') == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('is_voter') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="precinctContainer">
                                    <label class="form-label fw-semibold">Voter Precinct No.</label>
                                    <input type="text" name="voter_precinct_no" class="form-control @error('voter_precinct_no') is-invalid @enderror" value="{{ old('voter_precinct_no') }}">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary px-4" onclick="nextStep(1)"><i class="bi bi-arrow-left me-1"></i> Back</button>
                                <button type="button" class="btn btn-primary px-4" onclick="nextStep(3)">Next: Emergency Contact <i class="bi bi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        <!-- STEP 3: Emergency Contact & Medical Info -->
                        <div class="step-section d-none" id="step3">
                            <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Step 3: Emergency Contact & Medical Info</h5>
                            <p class="text-muted small">Please provide someone we can contact in case of an emergency, and your blood type for medical reference.</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Contact Person Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" value="{{ old('emergency_contact_name') }}" placeholder="Full Name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Relationship <span class="text-danger">*</span></label>
                                    <input type="text" name="emergency_contact_relation" class="form-control @error('emergency_contact_relation') is-invalid @enderror" value="{{ old('emergency_contact_relation') }}" placeholder="e.g. Spouse, Parent, Sibling" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Emergency Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="emergency_contact_number" class="form-control @error('emergency_contact_number') is-invalid @enderror" value="{{ old('emergency_contact_number') }}" placeholder="09171234567" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Blood Type</label>
                                    <select name="blood_type" class="form-select @error('blood_type') is-invalid @enderror">
                                        <option value="">-- Select Blood Type --</option>
                                        <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary px-4" onclick="nextStep(2)"><i class="bi bi-arrow-left me-1"></i> Back</button>
                                <button type="button" class="btn btn-primary px-4" onclick="nextStep(4)">Next: Verification IDs <i class="bi bi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        <!-- STEP 4: Identity Verification Uploads -->
                        <div class="step-section d-none" id="step4">
                            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-file-earmark-check-fill me-2"></i>Step 4: Identity Verification</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Valid ID Type <span class="text-danger">*</span></label>
                                    <select name="id_type" class="form-select @error('id_type') is-invalid @enderror" required>
                                        <option value="Philippine Identification (PhilID)" {{ old('id_type') == 'Philippine Identification (PhilID)' ? 'selected' : '' }}>Philippine Identification (PhilID)</option>
                                        <option value="Driver's License" {{ old('id_type') == "Driver's License" ? 'selected' : '' }}>Driver's License</option>
                                        <option value="UMID Card" {{ old('id_type') == 'UMID Card' ? 'selected' : '' }}>UMID Card</option>
                                        <option value="Passport" {{ old('id_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
                                        <option value="Voter's ID" {{ old('id_type') == "Voter's ID" ? 'selected' : '' }}>Voter's ID</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">ID Card Number <span class="text-danger">*</span></label>
                                    <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">ID Card (Front Photo) <span class="text-danger">*</span></label>
                                    <input type="file" name="id_front" class="form-control @error('id_front') is-invalid @enderror" accept="image/*" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">ID Card (Back Photo) <span class="text-danger">*</span></label>
                                    <input type="file" name="id_back" class="form-control @error('id_back') is-invalid @enderror" accept="image/*" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Proof of Residency (Utility Bill / Lease Contract) <span class="text-danger">*</span></label>
                                    <input type="file" name="proof_of_residency" class="form-control @error('proof_of_residency') is-invalid @enderror" accept="image/*,.pdf" required>
                                </div>
                            </div>

                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="terms" required id="termsCheck" {{ old('terms') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small" for="termsCheck">
                                    I hereby certify that all information provided is true and correct under the Data Privacy Act.
                                </label>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary px-4" onclick="nextStep(3)"><i class="bi bi-arrow-left me-1"></i> Back</button>
                                <button type="submit" class="btn btn-success fw-bold px-4"><i class="bi bi-check-circle me-1"></i> Submit Registration</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentStep = 1;

    document.addEventListener('DOMContentLoaded', function () {
        const voterSelect = document.getElementById('voterSelect');
        if (voterSelect) {
            togglePrecinct(voterSelect.value);
        }
    });

    function validateCurrentStep(step) {
        const stepContainer = document.getElementById('step' + step);
        const inputs = stepContainer.querySelectorAll('input, select, textarea');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.reportValidity();
                isValid = false;
                return false;
            }
        });

        return isValid;
    }

    function nextStep(targetStep) {
        if (targetStep > currentStep && !validateCurrentStep(currentStep)) {
            return;
        }

        currentStep = targetStep;

        document.querySelectorAll('.step-section').forEach(el => el.classList.add('d-none'));
        document.getElementById('step' + targetStep).classList.remove('d-none');
        
        const progressBar = document.getElementById('stepProgress');
        const ind1 = document.getElementById('stepIndicator1');
        const ind2 = document.getElementById('stepIndicator2');
        const ind3 = document.getElementById('stepIndicator3');
        const ind4 = document.getElementById('stepIndicator4');

        // Reset all indicators to secondary
        [ind1, ind2, ind3, ind4].forEach(ind => ind.className = 'btn btn-sm btn-secondary rounded-circle');

        if (targetStep === 1) {
            progressBar.style.width = '25%';
            ind1.className = 'btn btn-sm btn-primary rounded-circle';
        } else if (targetStep === 2) {
            progressBar.style.width = '50%';
            ind1.className = 'btn btn-sm btn-primary rounded-circle';
            ind2.className = 'btn btn-sm btn-primary rounded-circle';
        } else if (targetStep === 3) {
            progressBar.style.width = '75%';
            ind1.className = 'btn btn-sm btn-primary rounded-circle';
            ind2.className = 'btn btn-sm btn-primary rounded-circle';
            ind3.className = 'btn btn-sm btn-primary rounded-circle';
        } else if (targetStep === 4) {
            progressBar.style.width = '100%';
            ind1.className = 'btn btn-sm btn-primary rounded-circle';
            ind2.className = 'btn btn-sm btn-primary rounded-circle';
            ind3.className = 'btn btn-sm btn-primary rounded-circle';
            ind4.className = 'btn btn-sm btn-primary rounded-circle';
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function navigateToStep(targetStep) {
        if (targetStep < currentStep) {
            nextStep(targetStep);
        } else if (targetStep === currentStep + 1) {
            nextStep(targetStep);
        }
    }

    function togglePrecinct(isVoter) {
        const precinctContainer = document.getElementById('precinctContainer');
        const precinctInput = precinctContainer.querySelector('input');
        
        if (isVoter === "1") {
            precinctContainer.classList.remove('d-none');
        } else {
            precinctContainer.classList.add('d-none');
            if (precinctInput) precinctInput.value = '';
        }
    }
</script>
@endpush
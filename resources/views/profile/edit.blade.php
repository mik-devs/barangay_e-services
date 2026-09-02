@extends('layouts.app')

@section('title', 'Profile Settings - Barangay E-Portal')

@section('content')
@php
    function getDocUrl($path) {
        if (!$path) return null;
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        return asset('storage/' . ltrim(str_replace(['public/', 'storage/'], '', $path), '/'));
    }

    $idFrontUrl = getDocUrl($profile->id_front_path ?? null);
    $idBackUrl  = getDocUrl($profile->id_back_path ?? null);
    $proofUrl   = getDocUrl($profile->proof_of_residency_path ?? null);
    
    $accountStatus = strtolower($user->account_status ?? 'pending');
@endphp

<div class="container py-4">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Profile Settings</h3>
        <p class="text-muted small mb-0">Manage your account information, personal details, and verification documents.</p>
    </div>

    {{-- Account Status Banner --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">Account Verification Status</h5>
                <p class="text-muted small mb-0">Updating your profile information will submit it for admin review.</p>
            </div>
            <div>
                @if($accountStatus === 'verified' || $accountStatus === 'approved')
                    <span class="badge bg-success-subtle text-success border border-success fs-6 rounded-pill px-3 py-2">
                        <i class="bi bi-check-circle-fill me-1"></i> Verified Resident
                    </span>
                @elseif($accountStatus === 'rejected')
                    <span class="badge bg-danger-subtle text-danger border border-danger fs-6 rounded-pill px-3 py-2">
                        <i class="bi bi-x-circle-fill me-1"></i> Rejected
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning fs-6 rounded-pill px-3 py-2">
                        <i class="bi bi-clock-history me-1"></i> Pending Review
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Flash Alert --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Profile updated successfully! Submitted for verification.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <strong class="fs-6">Failed to save profile! Please fix the following errors:</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-10">
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                {{-- Card 1: Account & Avatar --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-circle text-primary me-2"></i>Account & Avatar</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-bold">Profile Photo</label>
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                                
                                <div class="position-relative flex-shrink-0">
                                    @php
                                        $cleanProfilePhoto = $user->profile_photo ? ltrim(str_replace(['public/', 'storage/'], '', $user->profile_photo), '/') : null;
                                    @endphp

                                    @if($cleanProfilePhoto && \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanProfilePhoto))
                                        <img src="{{ asset('storage/' . $cleanProfilePhoto) }}" 
                                             alt="{{ $user->first_name ?? 'User' }}" 
                                             class="rounded-circle shadow-sm border border-2 border-white" 
                                             width="80" 
                                             height="80" 
                                             style="object-fit: cover;">
                                    @else
                                        @php
                                            $firstLetter = !empty($user->first_name) ? strtoupper(substr($user->first_name, 0, 1)) : 'U';
                                        @endphp
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm border border-2 border-white flex-shrink-0" 
                                             style="width: 80px; height: 80px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); font-family: 'Inter', system-ui, sans-serif; font-weight: 700; font-size: 2rem;">
                                            {{ $firstLetter }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-grow-1">
                                    <input type="file" 
                                           name="profile_photo" 
                                           id="profile_photo" 
                                           class="form-control rounded-3 @error('profile_photo') is-invalid @enderror" 
                                           accept="image/*">
                                    
                                    <div class="form-text text-muted small mt-1">
                                        Allowed formats: JPG, PNG, WEBP (Max 2MB).
                                    </div>

                                    @error('profile_photo')
                                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control rounded-3 @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $user->phone_number) }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Card 2: Personal Details --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-card-heading text-primary me-2"></i>Personal Details</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" name="first_name" class="form-control rounded-3 @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control rounded-3 @error('middle_name') is-invalid @enderror" value="{{ old('middle_name', $user->middle_name) }}">
                            @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" name="last_name" class="form-control rounded-3 @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Suffix</label>
                            <input type="text" name="suffix" class="form-control rounded-3" value="{{ old('suffix', $user->suffix) }}" placeholder="e.g. N/A, Jr.">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Birth Date</label>
                            <input type="date" name="birth_date" class="form-control rounded-3 @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', optional($profile)->birth_date ? (is_string($profile->birth_date) ? $profile->birth_date : $profile->birth_date->format('Y-m-d')) : '') }}" required>
                            @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Gender</label>
                            <select name="gender" class="form-select rounded-3 @error('gender') is-invalid @enderror">
                                <option value="Male" {{ old('gender', $profile->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $profile->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Civil Status</label>
                            <select name="civil_status" class="form-select rounded-3 @error('civil_status') is-invalid @enderror">
                                @foreach(['Single', 'Married', 'Widowed', 'Separated'] as $cStatus)
                                    <option value="{{ $cStatus }}" {{ old('civil_status', $profile->civil_status ?? '') == $cStatus ? 'selected' : '' }}>
                                        {{ $cStatus }}
                                    </option>
                                @endforeach
                            </select>
                            @error('civil_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Blood Type</label>
                            <select name="blood_type" class="form-select rounded-3 @error('blood_type') is-invalid @enderror">
                                <option value="">Select Blood Type</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bType)
                                    <option value="{{ $bType }}" {{ old('blood_type', $profile->blood_type ?? '') == $bType ? 'selected' : '' }}>
                                        {{ $bType }}
                                    </option>
                                @endforeach
                            </select>
                            @error('blood_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Citizenship</label>
                            <input type="text" name="citizenship" class="form-control rounded-3" value="{{ old('citizenship', $profile->citizenship ?? 'Filipino') }}">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-bold">Occupation</label>
                            <input type="text" name="occupation" class="form-control rounded-3" value="{{ old('occupation', $profile->occupation ?? '') }}" placeholder="e.g. None, Teacher, Engineer">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">House Number</label>
                            <input type="text" name="house_number" class="form-control rounded-3" value="{{ old('house_number', $profile->house_number ?? '') }}" placeholder="e.g. 1245">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Street Name</label>
                            <input type="text" name="street" class="form-control rounded-3" value="{{ old('street', $profile->street ?? '') }}" placeholder="e.g. Mayas">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Purok / Sitio</label>
                            <input type="text" name="purok_sitio" class="form-control rounded-3" value="{{ old('purok_sitio', $profile->purok_sitio ?? '') }}" placeholder="e.g. Nodilla">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Voter Precinct No.</label>
                            <input type="text" name="voter_precinct_no" class="form-control rounded-3" value="{{ old('voter_precinct_no', $profile->voter_precinct_no ?? '') }}" placeholder="e.g. 123456">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold">Place of Birth</label>
                            <input type="text" name="birth_place" class="form-control rounded-3" value="{{ old('birth_place', $profile->birth_place ?? '') }}" placeholder="e.g. Pasig, Kiblawan Davao del Sur">
                        </div>
                    </div>
                </div>

                {{-- Card X: Emergency Contact Details --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-telephone-plus text-primary me-2"></i>Emergency Contact Details</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Emergency Contact Person</label>
                            <input type="text" name="emergency_contact_name" class="form-control rounded-3 @error('emergency_contact_name') is-invalid @enderror" value="{{ old('emergency_contact_name', $profile->emergency_contact_name ?? '') }}" placeholder="Full Name of Contact Person">
                            @error('emergency_contact_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Contact Number</label>
                            <input type="text" name="emergency_contact_number" class="form-control rounded-3 @error('emergency_contact_number') is-invalid @enderror" value="{{ old('emergency_contact_number', $profile->emergency_contact_number ?? '') }}" placeholder="e.g. 09123456789">
                            @error('emergency_contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Relationship</label>
                            <input type="text" name="emergency_contact_relation" class="form-control rounded-3 @error('emergency_contact_relation') is-invalid @enderror" value="{{ old('emergency_contact_relation', $profile->emergency_contact_relation ?? '') }}" placeholder="e.g. Mother, Spouse">
                            @error('emergency_contact_relation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Card 3: Verification Documents --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-check text-primary me-2"></i>Verification Documents</h5>
                    <p class="text-muted small mb-4">Upload clear copies of your ID and proof of residency.</p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ID Type</label>
                            <input type="text" name="id_type" class="form-control rounded-3 @error('id_type') is-invalid @enderror" placeholder="e.g. Voter's ID" value="{{ old('id_type', $profile->id_type ?? '') }}">
                            @error('id_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ID Number</label>
                            <input type="text" name="id_number" class="form-control rounded-3 @error('id_number') is-invalid @enderror" placeholder="e.g. 12345" value="{{ old('id_number', $profile->id_number ?? '') }}">
                            @error('id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Front ID --}}
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <label class="fw-bold mb-1 text-dark d-block">1. ID Front Path</label>
                                    <input type="file" name="id_front_path" class="form-control form-control-sm rounded-2 @error('id_front_path') is-invalid @enderror" accept="image/*">
                                    @error('id_front_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if($idFrontUrl)
                                    <div class="mt-3 pt-2 border-top text-center">
                                        <span class="d-block text-muted small mb-1">Current Front ID:</span>
                                        <img src="{{ $idFrontUrl }}" class="img-thumbnail rounded-3 w-100" style="height: 100px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Back ID --}}
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <label class="fw-bold mb-1 text-dark d-block">2. ID Back Path</label>
                                    <input type="file" name="id_back_path" class="form-control form-control-sm rounded-2 @error('id_back_path') is-invalid @enderror" accept="image/*">
                                    @error('id_back_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if($idBackUrl)
                                    <div class="mt-3 pt-2 border-top text-center">
                                        <span class="d-block text-muted small mb-1">Current Back ID:</span>
                                        <img src="{{ $idBackUrl }}" class="img-thumbnail rounded-3 w-100" style="height: 100px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Proof of Residency --}}
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <label class="fw-bold mb-1 text-dark d-block">3. Proof of Residency</label>
                                    <input type="file" name="proof_of_residency_path" class="form-control form-control-sm rounded-2 @error('proof_of_residency_path') is-invalid @enderror" accept="image/*,.pdf">
                                    @error('proof_of_residency_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @if($proofUrl)
                                    <div class="mt-3 pt-2 border-top text-center">
                                        <span class="d-block text-muted small mb-1">Current Proof Document:</span>
                                        @php
                                            $extension = pathinfo(parse_url($proofUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                                        @endphp

                                        @if(strtolower($extension) === 'pdf')
                                            <a href="{{ $proofUrl }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> View PDF Document
                                            </a>
                                        @else
                                            <img src="{{ $proofUrl }}" class="img-thumbnail rounded-3 w-100" style="height: 100px; object-fit: cover;">
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-save me-1"></i> Save Changes & Submit
                    </button>
                </div>
            </form>

            {{-- Card 4: Password Update --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-primary me-2"></i>Update Password</h5>
                
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Password</label>
                        <input type="password" name="current_password" class="form-control rounded-3" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                    </div>

                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-semibold">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
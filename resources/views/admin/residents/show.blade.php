@extends('layouts.admin')

@section('title', 'Resident Profile Details')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Resident Profile Details</h1>
            <p class="text-muted small mb-0">Comprehensive information and verification documents of the resident.</p>
        </div>
        <div>
            <a href="{{ route('admin.residents.index') }}" class="btn btn-light border shadow-sm px-3 py-2 rounded-3 small fw-medium text-dark d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to Residents
            </a>
        </div>
    </div>

    <!-- Success/Error Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $profile = $resident->profile ?? $resident->residentProfile ?? null;
        
        function getDocUrl($path) {
            if (empty($path)) return null;
            if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
            if (str_starts_with($path, 'storage/')) return asset($path);
            return asset('storage/' . $path);
        }

        $userAvatar = $resident->profile_picture ?? $resident->avatar ?? ($profile->profile_picture ?? $profile->avatar ?? null);
        $profilePic = getDocUrl($userAvatar);
    @endphp

    <div class="row g-4">
        <!-- Left Column: Personal Information -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="text-center pb-4 border-bottom">
                    <!-- User Profile Picture or Default Icon -->
                    <div class="mx-auto mb-3 shadow-sm rounded-circle overflow-hidden bg-light d-flex align-items-center justify-content-center border" style="width: 90px; height: 90px;">
                        @if($profilePic)
                            <img src="{{ $profilePic }}" alt="Profile Picture" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="bg-primary bg-opacity-10 text-primary w-100 h-100 d-flex align-items-center justify-content-center" style="font-size: 2.2rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        @endif
                    </div>

                    <h5 class="fw-bold text-dark mb-1">
                        {{ trim(($resident->first_name ?? '') . ' ' . ($resident->middle_name ?? '') . ' ' . ($resident->last_name ?? '')) ?: ($resident->name ?? 'Resident Profile') }}
                    </h5>
                    <p class="text-muted small mb-2">{{ $resident->email ?? 'No email provided' }}</p>
                    
                    @php
                        $status = strtolower($resident->account_status ?? 'active');
                        $badgeBg = ($status == 'active' || $status == 'verified') ? 'bg-success text-white' : ($status == 'pending' ? 'bg-warning text-dark' : 'bg-danger text-white');
                    @endphp
                    <div class="mb-3">
                        <span class="badge {{ $badgeBg }} rounded-pill px-3 py-1 fw-semibold text-capitalize">
                            {{ ucfirst($resident->account_status ?? 'Active') }}
                        </span>
                    </div>

                    <!-- ADMIN VERIFICATION / APPROVAL BUTTONS -->
                    @if($status == 'pending')
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <!-- Button para i-Verify -->
                            <form action="{{ route('admin.residents.approve', $resident->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Verify
                                </button>
                            </form>

                            <!-- Button para i-Reject -->
                            <form action="{{ route('admin.residents.reject', $resident->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this resident?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                                    <i class="bi bi-x-circle me-1"></i> Reject
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            @if($status != 'active' && $status != 'verified')
                                <form action="{{ route('admin.residents.approve', $resident->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3 small">
                                        <i class="bi bi-check-circle me-1"></i> Verify
                                    </button>
                                </form>
                            @endif
                            @if($status != 'inactive' && $status != 'rejected')
                                <form action="{{ route('admin.residents.reject', $resident->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject/deactivate this resident?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 small">
                                        <i class="bi bi-x-circle me-1"></i> Reject
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="pt-3 row g-3">
                    @if($profile)
                        <div class="col-6">
                            <span class="text-muted fs-7 d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Phone Number</span>
                            <span class="fw-medium text-dark small">{{ $resident->phone_number ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted fs-7 d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Gender</span>
                            <span class="fw-medium text-dark small">{{ $profile->gender ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted fs-7 d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Birth Date</span>
                            <span class="fw-medium text-dark small">{{ $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->format('M d, Y') : '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted fs-7 d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Civil Status</span>
                            <span class="fw-medium text-dark small">{{ $profile->civil_status ?? '-' }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-7 d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Birth Place</span>
                            <span class="fw-medium text-dark small">{{ $profile->birth_place ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted fs-7 d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Citizenship / Occupation</span>
                            <span class="fw-medium text-dark small">{{ $profile->citizenship ?? '-' }} / {{ $profile->occupation ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted fs-7 d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Voter Status</span>
                            <span class="fw-medium text-dark small">{{ ($profile->is_voter ?? 0) == 1 ? 'Registered (Precinct: ' . ($profile->voter_precinct_no ?? '-') . ')' : 'Not a Voter' }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-7 d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Address (House #, Street, Purok)</span>
                            <span class="fw-medium text-dark small">
                                @php
                                    $houseNo = trim($profile->house_number ?? '');
                                    $street = trim($profile->street ?? '');
                                    $purokRaw = trim($profile->purok_sitio ?? '');
                                    $purokClean = trim(preg_replace('/^purok\s*/i', '', $purokRaw));
                                    
                                    $fullAddress = collect([
                                            $houseNo ? $houseNo : null,
                                            $street ? $street . ' St.' : null,
                                            $purokClean ? 'Purok ' . $purokClean : null
                                        ])->filter()->implode(', ');
                                @endphp
                                
                                {{ $fullAddress ?: '-' }}
                            </span>
                        </div>
                    @else
                        <div class="col-12 text-center py-4 text-muted">
                            <i class="bi bi-info-circle fs-3 mb-2 d-block text-warning"></i>
                            <p class="small mb-0 fw-medium text-dark">This resident has not completed their profile setup yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Uploaded Documents Grid -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-richtext text-primary"></i> Uploaded Verification Documents
                </h5>

                @if($profile)
                    @php
                        $frontId = getDocUrl($profile->id_front_path ?? null);
                        $backId = getDocUrl($profile->id_back_path ?? null);
                        $proofImg = getDocUrl($profile->proof_of_residency_path ?? null);
                    @endphp

                    <div class="row g-4">
                        <!-- Front ID -->
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-light h-100 d-flex flex-column justify-content-between shadow-sm">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 small text-uppercase">{{ $profile->id_type ?? 'Valid ID' }} (Front)</h6>
                                </div>
                                <div class="text-center my-3" style="min-height: 160px; display: flex; align-items: center; justify-content: center;">
                                    @if($frontId)
                                        <a href="{{ $frontId }}" target="_blank" title="Click to view full image">
                                            <img src="{{ $frontId }}" alt="Front ID" class="img-fluid rounded-3 shadow-sm border bg-white" style="max-height: 150px; object-fit: contain;">
                                        </a>
                                    @else
                                        <div class="text-muted small py-4">
                                            <i class="bi bi-image fs-1 d-block opacity-50 mb-2"></i>
                                            <span>No Front ID uploaded</span>
                                        </div>
                                    @endif
                                </div>
                                @if($frontId)
                                    <div class="text-end">
                                        <a href="{{ $frontId }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small">
                                            <i class="bi bi-arrows-fullscreen me-1"></i> View Fullscreen
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Back ID -->
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-light h-100 d-flex flex-column justify-content-between shadow-sm">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 small text-uppercase">{{ $profile->id_type ?? 'Valid ID' }} (Back)</h6>
                                </div>
                                <div class="text-center my-3" style="min-height: 160px; display: flex; align-items: center; justify-content: center;">
                                    @if($backId)
                                        <a href="{{ $backId }}" target="_blank" title="Click to view full image">
                                            <img src="{{ $backId }}" alt="Back ID" class="img-fluid rounded-3 shadow-sm border bg-white" style="max-height: 150px; object-fit: contain;">
                                        </a>
                                    @else
                                        <div class="text-muted small py-4">
                                            <i class="bi bi-image fs-1 d-block opacity-50 mb-2"></i>
                                            <span>No Back ID uploaded</span>
                                        </div>
                                    @endif
                                </div>
                                @if($backId)
                                    <div class="text-end">
                                        <a href="{{ $backId }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small">
                                            <i class="bi bi-arrows-fullscreen me-1"></i> View Fullscreen
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Proof of Residency -->
                        <div class="col-12">
                            <div class="border rounded-4 p-3 bg-light d-flex flex-column justify-content-between shadow-sm">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 small text-uppercase">Proof of Residency</h6>
                                </div>
                                <div class="text-center my-3" style="min-height: 160px; display: flex; align-items: center; justify-content: center;">
                                    @if($proofImg)
                                        <a href="{{ $proofImg }}" target="_blank" title="Click to view full image">
                                            <img src="{{ $proofImg }}" alt="Proof Image" class="img-fluid rounded-3 shadow-sm border bg-white" style="max-height: 200px; object-fit: contain;">
                                        </a>
                                    @else
                                        <div class="text-muted small py-4">
                                            <i class="bi bi-file-earmark-image fs-1 d-block opacity-50 mb-2"></i>
                                            <span>No proof of residency uploaded</span>
                                        </div>
                                    @endif
                                </div>
                                @if($proofImg)
                                    <div class="text-end">
                                        <a href="{{ $proofImg }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small">
                                            <i class="bi bi-arrows-fullscreen me-1"></i> View Fullscreen
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5 my-auto text-muted">
                        <i class="bi bi-folder-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                        <h6 class="fw-bold text-dark">No verification documents available</h6>
                        <p class="small text-muted mb-0">The resident has not uploaded any identification or proof documents yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
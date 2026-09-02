@extends('layouts.app')

@section('title', 'Official Digital Barangay ID - Barangay E-Portal')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            {{-- Navigation & Actions --}}
            <div class="mb-4 d-flex justify-content-between align-items-center no-print">
                <a href="{{ route('profile.edit') }}" class="text-decoration-none small fw-semibold text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Profile Settings
                </a>
                <button onclick="window.print()" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-printer me-1"></i> Print / Download ID
                </button>
            </div>

            @php
                $cleanProfilePhoto = $user->profile_photo ? ltrim(str_replace(['public/', 'storage/'], '', $user->profile_photo), '/') : null;
                
                // Kinukuha ang kapitan mula sa database gamit ang role na 'captain'
                $captainUser = \App\Models\User::where('role', 'captain')->first();

                if ($captainUser) {
                    $captainName = trim($captainUser->first_name . ' ' . ($captainUser->middle_name ? strtoupper(substr($captainUser->middle_name, 0, 1)) . '. ' : '') . $captainUser->last_name . ($captainUser->suffix && $captainUser->suffix !== 'N/A' ? ' ' . $captainUser->suffix : ''));
                    $captainSigPath = $captainUser->signature ? $captainUser->signature : 'signatures/admin_sig_9_1785244746.png';
                } else {
                    $captainName = "HON. MIKKO C. MOMO";
                    $captainSigPath = 'signatures/admin_sig_9_1785244746.png';
                }

                $fullName = trim(($user->first_name ?? '') . ' ' . ($user->middle_name ? strtoupper(substr($user->middle_name, 0, 1)) . '. ' : '') . ($user->last_name ?? '') . ($user->suffix && $user->suffix !== 'N/A' ? ' ' . $user->suffix : ''));
                
                $address = trim(
                    ($profile->house_number ? $profile->house_number . ', ' : '') . 
                    ($profile->street ? $profile->street . ' St., ' : '') . 
                    ($profile->purok_sitio ? $profile->purok_sitio : '')
                );
            @endphp

            {{-- OFFICIAL BARANGAY ID CONTAINER (PVC ID LOOK) --}}
            <div class="card border-2 border-success shadow-lg rounded-4 overflow-hidden bg-white text-dark position-relative id-card-container">
                
                {{-- ID Header (Barangay Branding) --}}
                <div class="text-white p-3 text-center position-relative" style="background: linear-gradient(135deg, #1b4d3e 0%, #2e7d32 100%);">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-patch-check-fill text-warning fs-4"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-uppercase text-white" style="font-size: 0.75rem; letter-spacing: 1px;">Republic of the Philippines</h6>
                            <h5 class="fw-bold mb-0 text-uppercase text-white" style="letter-spacing: 0.5px; font-size: 1.1rem;">Barangay E-Portal</h5>
                        </div>
                    </div>
                    <span class="badge bg-warning text-dark mt-2 px-3 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">OFFICIAL RESIDENT ID</span>
                </div>

                <div class="card-body p-4 bg-white text-dark">
                    
                    <div class="row align-items-center g-3">
                        {{-- Avatar Column --}}
                        <div class="col-4 text-center">
                            <div class="position-relative d-inline-block">
                                @if($cleanProfilePhoto && \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanProfilePhoto))
                                    <img src="{{ asset('storage/' . $cleanProfilePhoto) }}" 
                                         alt="{{ $fullName }}" 
                                         class="rounded-3 shadow-sm border border-2 border-success" 
                                         width="100" 
                                         height="120" 
                                         style="object-fit: cover;">
                                @else
                                    @php
                                        $firstLetter = !empty($user->first_name) ? strtoupper(substr($user->first_name, 0, 1)) : 'U';
                                    @endphp
                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm border border-2 border-success mx-auto" 
                                         style="width: 100px; height: 120px; background: linear-gradient(135deg, #1b4d3e 0%, #2e7d32 100%); font-weight: 700; font-size: 2.5rem;">
                                        {{ $firstLetter }}
                                    </div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-success text-white px-2 py-1 rounded-1 fw-bold" style="font-size: 0.65rem;">
                                    <i class="bi bi-shield-fill-check me-1"></i>VERIFIED
                                </span>
                            </div>
                        </div>

                        {{-- Details Column --}}
                        <div class="col-8">
                            <div class="border-bottom border-secondary-subtle pb-2 mb-2">
                                <span class="d-block fw-semibold text-secondary" style="font-size: 0.7rem; text-transform: uppercase;">Resident Name</span>
                                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.15rem; line-height: 1.2;">{{ $fullName }}</h4>
                            </div>

                            <div class="row g-2 text-start" style="font-size: 0.8rem;">
                                <div class="col-6">
                                    <span class="d-block fw-semibold text-secondary" style="font-size: 0.7rem;">Birth Date</span>
                                    <strong class="text-dark">{{ optional($profile)->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->format('M d, Y') : 'N/A' }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="d-block fw-semibold text-secondary" style="font-size: 0.7rem;">Gender / Civil Status</span>
                                    <strong class="text-dark">{{ $profile->gender ?? 'N/A' }} / {{ $profile->civil_status ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="d-block fw-semibold text-secondary" style="font-size: 0.7rem;">Blood Type</span>
                                    <strong class="text-danger fw-bold">{{ $profile->blood_type ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-12 mt-1">
                                    <span class="d-block fw-semibold text-secondary" style="font-size: 0.7rem;">Address</span>
                                    <strong class="text-dark d-block">{{ $address ?: 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Secondary Info & Emergency Contact Divider --}}
                    <div class="mt-3 pt-3 border-top border-secondary-subtle">
                        <div class="row g-2 text-start" style="font-size: 0.8rem;">
                            <div class="col-6">
                                <span class="d-block fw-semibold text-secondary" style="font-size: 0.7rem;">Contact Number</span>
                                <strong class="text-dark">{{ $user->phone_number ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="d-block fw-semibold text-secondary" style="font-size: 0.7rem;">Voter Precinct No.</span>
                                <strong class="text-dark">{{ $profile->voter_precinct_no ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- EMERGENCY CONTACT SECTION (ICE) --}}
                    <div class="mt-3 p-3 rounded-3 border border-danger bg-danger-subtle bg-opacity-25 text-start">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-1 fs-6"></i>
                            <span class="fw-bold text-danger text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">In Case of Emergency (ICE)</span>
                        </div>
                        <div class="row g-1 text-dark" style="font-size: 0.8rem;">
                            <div class="col-7">
                                <span class="d-block fw-semibold text-secondary" style="font-size: 0.65rem;">Contact Person:</span>
                                <strong class="text-dark d-block">{{ $profile->emergency_contact_name ?? 'Not Specified' }}</strong>
                            </div>
                            <div class="col-5">
                                <span class="d-block fw-semibold text-secondary" style="font-size: 0.65rem;">Relationship:</span>
                                <strong class="text-dark d-block">{{ $profile->emergency_contact_relation ?? 'Not Specified' }}</strong>
                            </div>
                            <div class="col-12 mt-1">
                                <span class="d-block fw-semibold text-secondary" style="font-size: 0.65rem;">Emergency Number:</span>
                                <strong class="text-danger fw-bold fs-6">{{ $profile->emergency_contact_number ?? 'Not Specified' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- ID Footer Signature & Seal --}}
                    <div class="mt-3 pt-2 border-top border-secondary-subtle d-flex justify-content-between align-items-end" style="font-size: 0.7rem;">
                        <div>
                            <span class="text-dark fw-semibold d-block">ID No: <strong>BRGY-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</strong></span>
                            <span class="badge bg-success text-white mt-1 px-2 py-1" style="font-size: 0.6rem;">OFFICIAL & VALID</span>
                        </div>

                        {{-- Barangay Captain Signature & Name Section --}}
                        <div class="text-center position-relative" style="min-width: 150px;">
                            <div style="height: 35px; display: flex; align-items: center; justify-content: center;">
                                @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($captainSigPath))
                                    <img src="{{ asset('storage/' . $captainSigPath) }}" 
                                         alt="Captain Signature" 
                                         style="max-height: 35px; max-width: 130px; object-fit: contain;">
                                @else
                                    <span class="fst-italic text-danger" style="font-size: 0.6rem;">(File Not Found)</span>
                                @endif
                            </div>

                            <div class="border-top border-dark mt-1 pt-1">
                                <span class="fw-bold text-dark d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.3px;">{{ $captainName }}</span>
                                <span class="text-secondary d-block" style="font-size: 0.55rem;">PUNONG BARANGAY</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- CSS para sa maayos na pag-print --}}
@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .id-card-container, .id-card-container * {
        visibility: visible;
    }
    .id-card-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: 2px solid #1b4d3e !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endpush
@endsection
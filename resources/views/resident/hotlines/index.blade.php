@extends('layouts.app')

@section('title', 'Emergency Hotlines Directory - Barangay E-Portal')

@section('content')
<div class="container py-5">
    
    <!-- Modern Hero Header -->
    <div class="row mb-5 align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-3 py-2 rounded-pill mb-2">
                <i class="bi bi-shield-fill-exclamation me-1"></i> 24/7 Emergency Response
            </span>
            <h1 class="fw-extrabold text-dark display-6 mb-2">Emergency Hotlines Directory</h1>
            <p class="text-muted lead fs-6 mb-0">Click the copy icon or call button for quick access to essential community services.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('resident.dashboard') }}" class="btn btn-light border shadow-sm rounded-pill px-4 py-2 fw-semibold text-secondary hover-scale">
                <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search / Filter Bar -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border bg-white">
                <span class="input-group-text bg-transparent border-0 ps-4 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchHotline" class="form-control border-0 shadow-none ps-2 py-3" placeholder="Search agency or details...">
            </div>
        </div>
    </div>

    <!-- Hotlines Modern Grid -->
    <div class="row g-4" id="hotlinesGrid">
        @forelse($hotlines as $hotline)
            <div class="col-md-6 col-xl-4 hotline-item" data-name="{{ strtolower($hotline->agency_name) }}" data-description="{{ strtolower($hotline->description ?? '') }}">
                <div class="card border-0 shadow-sm rounded-4 h-100 hotline-card position-relative overflow-hidden">
                    <!-- Accent Top Line -->
                    <div class="position-absolute top-0 start-0 end-0 bg-danger" style="height: 4px;"></div>
                    
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-4 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-telephone-fill fs-4"></i>
                                </div>
                                <div>
                                    <span class="badge bg-light text-secondary border fw-semibold px-2 py-1 rounded-pill mb-1" style="font-size: 0.75rem;">
                                        Emergency Agency
                                    </span>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $hotline->agency_name }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-muted small mb-4 flex-grow-1">{{ $hotline->description ?? 'Barangay official emergency contact number ready to assist you.' }}</p>
                        
                        <!-- Number & Actions Box -->
                        <div class="bg-light p-3 rounded-4 d-flex align-items-center justify-content-between mt-auto border">
                            <div>
                                <span class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Contact Number</span>
                                <span class="fw-bold text-danger fs-5 hotline-num">{{ $hotline->contact_number }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <!-- Working Copy Button -->
                                <button type="button" class="btn btn-white border shadow-sm rounded-circle p-2 text-primary copy-btn" title="Copy Number" data-number="{{ $hotline->contact_number }}">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                                <!-- Direct Call Button -->
                                <a href="tel:{{ $hotline->contact_number }}" class="btn btn-danger rounded-circle p-2 text-white shadow-sm" title="Call Now">
                                    <i class="bi bi-telephone-outbound-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                    <div class="card-body py-5">
                        <div class="bg-light text-muted rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-inbox fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark">No Emergency Hotlines Found</h4>
                        <p class="text-muted mb-0">There are currently no emergency hotlines registered in the directory.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>

<!-- Floating Toast Notification for Copy Success -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="copyToast" class="toast align-items-center text-white bg-dark border-0 rounded-pill shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body px-4 py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i> Contact number successfully copied to clipboard!
            </div>
        </div>
    </div>
</div>

<style>
    .hotline-card {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .hotline-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.08) !important;
    }
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: scale(1.02);
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Real-time Search Filter
        const searchInput = document.getElementById('searchHotline');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                let query = this.value.toLowerCase();
                let items = document.querySelectorAll('.hotline-item');

                items.forEach(function(item) {
                    let name = item.getAttribute('data-name');
                    let desc = item.getAttribute('data-description');
                    if (name.includes(query) || desc.includes(query)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Fully Functioning Copy to Clipboard Logic
        const copyButtons = document.querySelectorAll('.copy-btn');
        const toastEl = document.getElementById('copyToast');
        const toast = toastEl ? new bootstrap.Toast(toastEl, { delay: 2000 }) : null;

        copyButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                let numberToCopy = this.getAttribute('data-number');
                
                // Modern clipboard API fallback support
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(numberToCopy).then(function() {
                        if (toast) toast.show();
                    }).catch(function(err) {
                        console.error('Failed to copy: ', err);
                    });
                } else {
                    // Fallback method for older browsers
                    let textArea = document.createElement("textarea");
                    textArea.value = numberToCopy;
                    textArea.style.position = "fixed";  // Avoid scrolling to bottom
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        if (toast) toast.show();
                    } catch (err) {
                        console.error('Fallback: Oops, unable to copy', err);
                    }
                    document.body.removeChild(textArea);
                }
            });
        });
    });
</script>
@endsection
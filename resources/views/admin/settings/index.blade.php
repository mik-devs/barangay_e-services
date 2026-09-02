@extends('layouts.admin')

@section('title', 'System Settings - Admin Panel')

@section('content')
<div class="container-fluid px-2 px-md-4 py-4">
    <!-- Header Section -->
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark mb-1">System Settings</h1>
        <p class="text-muted small mb-0">Manage general configurations, barangay details, and system preferences.</p>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span class="fw-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Settings Form Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <h5 class="fw-bold text-dark mb-3">General Information</h5>
            <p class="text-muted small mb-4">Update the basic details displayed across the barangay portal and document headers.</p>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label for="barangay_name" class="form-label small fw-semibold text-dark">Barangay Name</label>
                    <input type="text" class="form-control rounded-3 py-2" id="barangay_name" name="barangay_name" value="{{ old('barangay_name', $settings->barangay_name ?? '') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="municipality" class="form-label small fw-semibold text-dark">Municipality / City</label>
                    <input type="text" class="form-control rounded-3 py-2" id="municipality" name="municipality" value="{{ old('municipality', $settings->municipality ?? '') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="contact_number" class="form-label small fw-semibold text-dark">Official Contact Number</label>
                    <input type="text" class="form-control rounded-3 py-2" id="contact_number" name="contact_number" value="{{ old('contact_number', $settings->contact_number ?? '') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label for="email" class="form-label small fw-semibold text-dark">Official Email Address</label>
                    <input type="email" class="form-control rounded-3 py-2" id="email" name="email" value="{{ old('email', $settings->email ?? '') }}">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 small fw-semibold shadow-sm">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
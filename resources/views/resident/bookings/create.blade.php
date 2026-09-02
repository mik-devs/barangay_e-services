@extends('layouts.app')

@section('title', 'Reserve Facility - Barangay E-Portal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Reserve a Facility</h3>
                    <p class="text-muted small mb-0">Fill out the form below to book the Barangay Hall or Covered Court.</p>
                </div>
                <a href="{{ route('resident.bookings.index') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to History
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ route('resident.bookings.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Facility <span class="text-danger">*</span></label>
                        <select name="facility_name" class="form-select rounded-3 @error('facility_name') is-invalid @enderror" required>
                            <option value="" selected disabled>-- Choose Facility --</option>
                            <option value="Barangay Hall" {{ old('facility_name') == 'Barangay Hall' ? 'selected' : '' }}>Barangay Hall</option>
                            <option value="Covered Court" {{ old('facility_name') == 'Covered Court' ? 'selected' : '' }}>Barangay Covered Court</option>
                            <option value="Multi-Purpose Room" {{ old('facility_name') == 'Multi-Purpose Room' ? 'selected' : '' }}>Multi-Purpose Room</option>
                        </select>
                        @error('facility_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Booking Date <span class="text-danger">*</span></label>
                        <input type="date" name="booking_date" class="form-control rounded-3 @error('booking_date') is-invalid @enderror" value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}" required>
                        @error('booking_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control rounded-3 @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control rounded-3 @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Purpose / Event Name <span class="text-danger">*</span></label>
                        <textarea name="purpose" rows="3" class="form-control rounded-3 @error('purpose') is-invalid @enderror" placeholder="e.g., Family Birthday Celebration, Basketball League" required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-info text-white rounded-pill w-100 fw-semibold py-2">
                        Submit Reservation <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Edit Announcement - Admin Panel')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Edit Announcement</h1>
            <p class="text-muted small mb-0">Update information and details of the announcement.</p>
        </div>
        <div>
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-light border shadow-sm px-3 py-2 rounded-3 small fw-medium text-dark d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Title -->
                <div class="col-12">
                    <label for="title" class="form-label fw-semibold text-dark small">Announcement Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-3 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Priority Level -->
                <div class="col-md-6">
                    <label for="priority" class="form-label fw-semibold text-dark small">Priority Level <span class="text-danger">*</span></label>
                    <select class="form-select rounded-3 @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                        <option value="normal" {{ (old('priority', $announcement->priority) == 'normal') ? 'selected' : '' }}>Normal</option>
                        <option value="important" {{ (old('priority', $announcement->priority) == 'important') ? 'selected' : '' }}>Important</option>
                        <option value="urgent" {{ (old('priority', $announcement->priority) == 'urgent') ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div class="col-md-6">
                    <label for="image" class="form-label fw-semibold text-dark small">Banner Image (Optional)</label>
                    <input type="file" class="form-control rounded-3 @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                    <div class="form-text text-muted small">Leave blank if you don't want to change the current image.</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if($announcement->image)
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <span class="text-muted small">Current:</span>
                            <a href="{{ filter_var($announcement->image, FILTER_VALIDATE_URL) ? $announcement->image : asset('storage/' . $announcement->image) }}" target="_blank" class="small text-decoration-none fw-medium text-primary">
                                <i class="bi bi-image me-1"></i> View Existing Image
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Body Content -->
                <div class="col-12">
                    <label for="body" class="form-label fw-semibold text-dark small">Announcement Body / Content <span class="text-danger">*</span></label>
                    <textarea class="form-control rounded-3 @error('body') is-invalid @enderror" id="body" name="body" rows="6" required>{{ old('body', $announcement->body) }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-end pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="bi bi-check-lg"></i> Update Announcement
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
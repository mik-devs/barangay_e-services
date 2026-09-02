@extends('layouts.admin')

@section('title', 'Admin Digital Signature')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Digital Signature Management</h1>
            <p class="text-muted small mb-0">Draw and save your official signature to be displayed on approved documents.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 p-4" style="max-width: 600px;">
        @if(auth()->user()->signature && file_exists(public_path('storage/' . auth()->user()->signature)))
            <div class="mb-4">
                <label class="form-label small text-muted fw-bold d-block">Current Saved Signature:</label>
                <div class="p-3 bg-light border rounded-3 d-inline-block">
                    <img src="{{ asset('storage/' . auth()->user()->signature) }}" alt="Current Signature" style="max-height: 80px;">
                </div>
            </div>
        @endif

        <form action="{{ route('admin.signature.store') }}" method="POST" id="signature-form">
            @csrf
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Draw your signature below (Use mouse or touch):</label>
                <div class="border rounded-3 bg-light p-2 text-center">
                    <canvas id="signature-pad" class="border bg-white rounded-3 shadow-sm w-100" height="200" style="touch-action: none;"></canvas>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2 px-3" id="clear">Clear Canvas</button>
            </div>

            <input type="hidden" name="signature" id="signature-input">

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">Save Signature</button>
            </div>
        </form>
    </div>
</div>

<!-- Include Signature Pad JS Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    const canvas = document.querySelector("#signature-pad");
    
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }
    
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    const signaturePad = new SignaturePad(canvas);

    document.getElementById('clear').addEventListener('click', function () {
        signaturePad.clear();
    });

    document.getElementById('signature-form').addEventListener('submit', function (e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            alert("Please provide a signature before saving.");
            return;
        }
        document.getElementById('signature-input').value = signaturePad.toDataURL('image/png');
    });
</script>
@endsection
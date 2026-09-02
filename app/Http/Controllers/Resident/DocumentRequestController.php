<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\User;
use App\Notifications\ResidentPortalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi; 
use App\Models\BarangayOfficial;

class DocumentRequestController extends Controller
{
    // Display list of requests
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $query = DocumentRequest::where('user_id', auth()->id());

        if ($type !== 'all') {
            if ($type == 'Certificate of Clearance') {
                
                $query->where('document_type', 'like', '%Clearance%')
                      ->where('document_type', 'not like', '%Business%');
            } elseif ($type == 'Barangay Business Permit') {
                $query->where(function($q) {
                    $q->where('document_type', 'like', '%Business%')
                      ->orWhere('document_type', 'like', '%Permit%');
                });
            } elseif ($type == 'Certificate of Indigency') {
                $query->where('document_type', 'like', '%Indigency%');
            } elseif ($type == 'Certificate of Residency') {
                $query->where('document_type', 'like', '%Residency%');
            } else {
                $query->where('document_type', $type);
            }
        }

        $requests = $query->latest()->paginate(10);

        return view('resident.documents.index', compact('requests'));
    }

    // Show request form
    public function create()
    {
        return view('resident.documents.create');
    }

    // Process submitted request
    public function store(Request $request)
    {
        Log::info('Document Request Submission Started', [
            'all_input' => $request->except('id_attachment'),
            'has_file' => $request->hasFile('id_attachment')
        ]);

        $validated = $request->validate([
            'document_type' => 'required|string',
            'purpose'       => 'required|string|max:255',
            'remarks'       => 'nullable|string|max:500',
            'id_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('id_attachment')) {
            $file = $request->file('id_attachment');
            $filePath = $file->store('document_attachments', 'public');
            
            Log::info('Checking generated file path before DB insert: ' . $filePath);
        } else {
            Log::warning('No file detected under id_attachment during store execution.');
        }

        $documentRequest = DocumentRequest::create([
            'tracking_number' => 'BRGY-' . strtoupper(Str::random(8)),
            'user_id'         => auth()->id(),
            'document_type'   => $validated['document_type'],
            'purpose'         => $validated['purpose'],
            'remarks'         => $validated['remarks'] ?? null,
            'attachment'      => $filePath,
            'status'          => 'pending',
        ]);

        $user = auth()->user();
        $residentName = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if (empty($residentName)) {
            $residentName = 'A resident';
        }

        $admins = User::whereIn('role', ['admin', 'staff'])->get();
        Notification::send($admins, new ResidentPortalNotification(
            'New Document Request',
            "{$residentName} requested a {$documentRequest->document_type}.",
            route('admin.documents.show', $documentRequest->id)
        ));

        return redirect()->route('resident.documents.index')
            ->with('success', 'Your document request has been submitted successfully!');
    }

    // ========================================================================
    // [OPTION 2: ADMIN UPLOADS A PDF, SYSTEM AUTOMATICALLY ADDS SIGNATURE & NAME]
    // ========================================================================
    public function adminUploadAndSign(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'admin_pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('admin_pdf');
        $originalPath = $file->store('temp_uploads', 'public');
        $fullOriginalPath = storage_path('app/public/' . $originalPath);

        // 1. Kunin ang Punong Barangay o Admin na may naka-save na E-Signature
        $signatory = User::whereIn('role', ['captain', 'punong_barangay', 'admin'])
            ->whereNotNull('signature')
            ->latest()
            ->first();

        if ($signatory) {
            Log::info('Signatory found: ' . $signatory->name . ' (Role: ' . $signatory->role . ')');
            Log::info('Signature DB path: ' . $signatory->signature);
        } else {
            Log::warning('No signatory found with role [captain, punong_barangay, admin] and a valid signature.');
        }

        $sigPath = null;
        if ($signatory && $signatory->signature) {
            $possiblePath = storage_path('app/public/' . $signatory->signature);
            if (file_exists($possiblePath)) {
                $sigPath = $possiblePath;
                Log::info('Signature file successfully located at: ' . $sigPath);
            } else {
                Log::error('Signature file DOES NOT exist at physical path: ' . $possiblePath);
            }
        }

        $punongBarangayName = trim(($signatory->first_name ?? '') . ' ' . ($signatory->last_name ?? ''));
        if (empty($punongBarangayName)) {
            $punongBarangayName = $signatory->name ?? 'PUNONG BARANGAY';
        }

        // 2. Gamitin ang FPDI para basahin at patungan ang in-upload na PDF
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fullOriginalPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Kung ito na ang huling pahina ng dokumento, idikit ang pirma at pangalan
            if ($pageNo == $pageCount) {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Helvetica', 'B', 10);
                
                // Coordinates sa gitna-kanan ng ibabang bahagi
                $xSignature = 130; 
                $ySignature = 210; 

                // Idikit ang E-Signature image kung available
                if ($sigPath && file_exists($sigPath)) {
                    $ext = strtolower(pathinfo($sigPath, PATHINFO_EXTENSION));
                    $type = ($ext === 'png') ? 'PNG' : 'JPEG';
                    
                    try {
                        $pdf->Image($sigPath, $xSignature, $ySignature - 15, 40, 18, $type);
                        Log::info('Signature image successfully drawn on PDF at X:' . $xSignature . ' Y:' . $ySignature);
                    } catch (\Exception $e) {
                        Log::error('Failed to render signature image on PDF: ' . $e->getMessage());
                    }
                } else {
                    Log::warning('Skipping signature drawing because $sigPath is missing or file does not exist.');
                }

                // Linya sa ilalim ng pirma
                $pdf->SetXY($xSignature - 5, $ySignature + 3);
                $pdf->Cell(50, 4, '', 'T', 0, 'C'); 

                // Pangalan ng Punong Barangay
                $pdf->SetXY($xSignature - 5, $ySignature + 7);
                $pdf->Cell(50, 5, strtoupper($punongBarangayName), 0, 1, 'C');

                // Posisyon
                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->SetXY($xSignature - 5, $ySignature + 12);
                $pdf->Cell(50, 5, 'PUNONG BARANGAY', 0, 1, 'C');
            }
        }

        // 3. I-save ang bagong PDF
        $filename = 'signed-' . Str::slug($documentRequest->document_type) . '-' . $documentRequest->tracking_number . '.pdf';
        $outputPath = 'completed_documents/' . $filename;
        $fullOutputPath = storage_path('app/public/' . $outputPath);

        $pdf->Output($fullOutputPath, 'F');

        Storage::disk('public')->delete($originalPath);

        // 4. I-update ang Database
        $documentRequest->update([
            'status' => 'approved',
            'completed_document' => $outputPath,
        ]);

        return back()->with('success', 'Document uploaded and automatically signed by the Punong Barangay!');
    }

    // Download document function
    public function download(DocumentRequest $documentRequest)
    {
        if ($documentRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $payment = \App\Models\Payment::where('payable_id', $documentRequest->id)
            ->where(function($query) use ($documentRequest) {
                $query->where('payable_type', \get_class($documentRequest))
                      ->orWhere('payable_type', 'like', '%DocumentRequest%')
                      ->orWhere('payable_type', 'document_request');
            })
            ->first();

        if (!$payment || strtolower($payment->payment_status) !== 'paid') {
            return back()->with('error', 'This document cannot be downloaded yet. Please complete the payment first.');
        }

        if (!in_array($documentRequest->status, ['approved', 'completed', 'ready_for_pickup'])) {
            return back()->with('error', 'Your request is currently being processed or awaiting approval.');
        }

        // Kung may in-upload/na-sign nang completed document:
        if (!empty($documentRequest->completed_document) && Storage::disk('public')->exists($documentRequest->completed_document)) {
            $extension = pathinfo($documentRequest->completed_document, PATHINFO_EXTENSION);
            $filename = 'Official-' . Str::slug($documentRequest->document_type) . '-' . $documentRequest->tracking_number . '.' . $extension;
            return Storage::disk('public')->download($documentRequest->completed_document, $filename);
        }

        // Fallback kung wala pa: I-generate manually gamit ang Blade template
        $signatory = User::whereIn('role', ['captain', 'punong_barangay', 'admin'])->whereNotNull('signature')->latest()->first();

        $viewName = match(strtolower($documentRequest->document_type)) {
            'certificate of indigency', 'indigency' => 'admin.documents.indigency',
            'barangay clearance', 'clearance' => 'admin.documents.clearance',
            'certificate of residency', 'residency' => 'admin.documents.residency',
            'business permit', 'permit', 'business permit clearance' => 'admin.documents.business_permit', 
            default => 'admin.documents.clearance',
        };

        $pdf = Pdf::loadView($viewName, [
            'documentRequest' => $documentRequest,
            'signatory' => $signatory,
        ]);

        $filename = Str::slug($documentRequest->document_type) . '-' . $documentRequest->tracking_number . '.pdf';

        return $pdf->download($filename);
    }

    public function show(DocumentRequest $documentRequest)
    {
        if ($documentRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $documentRequest->load('user.residentProfile');

        return view('resident.documents.show', compact('documentRequest'));
    }
    public function printDocument($id)
{
    $documentRequest = DocumentRequest::with('resident')->findOrFail($id);
    
    
    $punongBarangay = BarangayOfficial::where('position', 'Punong Barangay')
                        ->where('status', 'active') 
                        ->first();

    
    $template = match($documentRequest->document_type) {
        'Clearance' => 'admin.documents.clearance',
        'Indigency' => 'admin.documents.indigency',
        'Residency' => 'admin.documents.residency',
        default => 'admin.documents.show',
    };

    return view($template, compact('documentRequest', 'punongBarangay'));
}
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\ActivityLog; 
use App\Models\Payment;
use App\Models\User;
use App\Notifications\ResidentPortalNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;
use FPDF;

class AdminDocumentController extends Controller
{
    /**
     * Display a listing of all document requests for admin and staff.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $documents = DocumentRequest::with('user')
            ->when($search, function ($query, $search) {
                if (is_numeric($search)) {
                    $query->where('id', $search);
                } else {
                    $query->where('document_type', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($subQ) use ($search) {
                              $subQ->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%");
                          });
                }
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Display the specified document request details.
     */
    public function show(DocumentRequest $documentRequest): View
    {
        $documentRequest->load('user.residentProfile');

        // Automatically generate default HTML template for Summernote if empty
        if (empty($documentRequest->certificate_content)) {
            $resident = $documentRequest->user;
            $profile = $resident ? $resident->residentProfile : null;
            
            $rawName = trim(($resident->first_name ?? '') . ' ' . ($resident->middle_name ?? '') . ' ' . ($resident->last_name ?? ''));
            $residentName = trim(str_ireplace('N/A', '', $rawName)) ?: ($resident->name ?? 'Resident');
            
            $purok = $profile ? ($profile->purok_sitio ?? 'Purok') : 'Purok';
            $purpose = $documentRequest->purpose ?? 'legal purposes it may serve';

            $documentRequest->certificate_content = "<p>This is to certify that <strong>{$residentName}</strong>, of legal age, Filipino, is a permanent resident of {$purok}, Barangay Kiblawan, Kiblawan, Davao del Sur.</p><p>This certification is issued upon the request of the above-named person for <strong>{$purpose}</strong>.</p>";
        }

        return view('admin.documents.show', compact('documentRequest'));
    }

    /**
     * Update the status, fee, certificate content, and details of a document request.
     */
    public function updateStatus(Request $request, DocumentRequest $documentRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status'              => ['nullable', 'in:pending,processing,approved,rejected,ready_for_pickup,completed'],
            'fee'                 => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'admin_remarks'       => ['nullable', 'string', 'max:500'],
            'pickup_date'         => ['nullable', 'date'],
            'certificate_content' => ['nullable', 'string'],
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'approved';
        }

        if ($validated['status'] === 'approved' && !$documentRequest->issued_at) {
            $validated['issued_at'] = now();
            if (empty($validated['pickup_date'])) {
                $validated['pickup_date'] = now()->addDays(3);
            }
        }

        $validated['fee'] = $validated['fee'] ?? $documentRequest->fee ?? 0;

        $documentRequest->update($validated);
        
        if ($validated['status'] === 'approved' && $validated['fee'] > 0) {
            Payment::updateOrCreate(
                [
                    'payable_id'   => $documentRequest->id,
                    'payable_type' => get_class($documentRequest),
                ],
                [
                    'resident_id'      => $documentRequest->user_id,
                    'amount'           => $validated['fee'],
                    'payment_status'   => 'pending',
                    'payment_method'   => 'cash', 
                    'reference_number' => $documentRequest->tracking_number ?? ('REF-' . strtoupper(uniqid())),
                ]
            );
        }

        $requesterName = $documentRequest->user ? ($documentRequest->user->first_name . ' ' . $documentRequest->user->last_name) : 'Unknown Resident';
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Document Status & Fee',
            'description' => 'Updated document request (' . $documentRequest->document_type . ') for ' . $requesterName . ' to status: ' . ucfirst($validated['status']) . ' with fee: ₱' . number_format($validated['fee'], 2),
        ]);

        if ($documentRequest->user) {
            $statusLabels = [
                'processing'       => 'is now being processed',
                'approved'         => 'has been approved' . ($validated['fee'] > 0 ? '. Please check your My Payments tab to pay the fee of ₱' . number_format($validated['fee'], 2) : ''),
                'ready_for_pickup' => 'is now ready for pickup',
                'completed'        => 'has been completed',
                'rejected'         => 'has been rejected',
            ];

            $statusText = $statusLabels[$documentRequest->status] ?? 'status has been updated to ' . $documentRequest->status;

            $documentRequest->user->notify(new ResidentPortalNotification(
                'Document Request Update',
                'Your request for ' . $documentRequest->document_type . ' ' . $statusText . '.',
                route('resident.documents.show', $documentRequest->id)
            ));
        }

        return back()->with('success', 'Document status, certificate content, and fee updated successfully, and resident has been notified!');
    }

    /**
     * Upload the completed or signed document file for the resident.
     */
    public function uploadCompletedDocument(Request $request, DocumentRequest $documentRequest): RedirectResponse
    {
        $request->validate([
            'completed_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('completed_document')) {
            if ($documentRequest->completed_document && Storage::disk('public')->exists($documentRequest->completed_document)) {
                Storage::disk('public')->delete($documentRequest->completed_document);
            }

            $file = $request->file('completed_document');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('completed_documents', $filename, 'public');

            $documentRequest->update([
                'completed_document' => $path,
                'status'             => 'completed', 
            ]);
        }

        return back()->with('success', 'The completed document has been successfully uploaded!');
    }

    /**
     * Generate and stream the Official PDF Document for printing.
     */
    public function generatePdf(DocumentRequest $documentRequest): Response
    {
        $documentRequest->load('user.residentProfile');

        $signatory = null;
        if (isset($documentRequest->approvedBy) && $documentRequest->approvedBy) {
            $signatory = $documentRequest->approvedBy;
        } else {
            $signatory = User::whereIn('role', ['captain', 'punong_barangay'])->whereNotNull('signature')->first();
            
            if (!$signatory) {
                $signatory = User::where('role', 'admin')->whereNotNull('signature')->first();
            }
        }

        $pdf = Pdf::loadView('pdf.official-document', [
            'doc'       => $documentRequest,
            'resident'  => $documentRequest->user,
            'profile'   => $documentRequest->user->residentProfile ?? null,
            'signatory' => $signatory,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("{$documentRequest->tracking_number}.pdf");
    }

    /**
     * Public QR Code Verification System.
     */
    public function verifyQrCode(string $tracking_number): View
    {
        $documentRequest = DocumentRequest::with(['user'])
            ->where('tracking_number', $tracking_number)
            ->firstOrFail();

        return view('public.verify-document', compact('documentRequest'));
    }

    /**
     * Display the form for the admin to draw/manage their digital signature.
     */
    public function signatureForm(): View
    {
        return view('admin.signature.index');
    }

    /**
     * Store the drawn digital signature into storage and update the admin user record.
     */
    public function storeSignature(Request $request): RedirectResponse
    {
        $request->validate([
            'signature' => ['required', 'string'],
        ]);

        $imgData = $request->input('signature');
        $imageParts = explode(';base64,', $imgData);
        
        if (count($imageParts) < 2) {
            return back()->with('error', 'Invalid signature format.');
        }

        $imageBase64 = base64_decode($imageParts[1]);
        $fileName = 'signatures/admin_sig_' . auth()->id() . '_' . time() . '.png';

        Storage::disk('public')->put($fileName, $imageBase64);

        $user = auth()->user();
        
        if ($user->signature && Storage::disk('public')->exists($user->signature)) {
            Storage::disk('public')->delete($user->signature);
        }

        $user->update(['signature' => $fileName]);

        return back()->with('success', 'Digital signature saved successfully!');
    }

    /**
     * Download the document as a Microsoft Word (.doc) file.
     */
    public function downloadWord(DocumentRequest $documentRequest)
    {
        $documentRequest->load('user.residentProfile');
        $resident = $documentRequest->user;
        
        $rawName = trim(($resident->first_name ?? '') . ' ' . ($resident->middle_name ?? '') . ' ' . ($resident->last_name ?? ''));
        $residentName = trim(str_ireplace('N/A', '', $rawName)) ?: ($resident->name ?? 'Resident');

        $content = $documentRequest->certificate_content ?: "This is to certify that {$residentName}...";

        $htmlContent = "
            <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head>
                <meta charset='utf-8'>
                <title>{$documentRequest->document_type}</title>
                <style>
                    body { font-family: 'Times New Roman', Times, serif; padding: 40px; font-size: 14pt; }
                    .header { text-align: center; line-height: 1.3; margin-bottom: 30px; }
                    .title { text-align: center; text-transform: uppercase; font-weight: bold; margin: 40px 0; font-size: 16pt; }
                    .content { line-height: 2.0; text-align: justify; }
                    .footer { margin-top: 100px; float: right; text-align: center; }
                </style>
            </head>
            <body>
                <div class='header'>
                    <p style='margin:0;'>Republic of the Philippines</p>
                    <p style='margin:0;'>Province of Davao del Sur</p>
                    <p style='margin:0;'>Municipality of Kiblawan</p>
                    <h3 style='margin: 5px 0 0 0;'>BARANGAY KIBLAWAN</h3>
                    <p style='margin:0; font-style: italic; font-size: 11pt;'>Office of the Punong Barangay</p>
                </div>
                
                <div class='title'>
                    {$documentRequest->document_type}
                </div>

                <p style='font-size: 11pt;'><b>Tracking No:</b> {$documentRequest->tracking_number}</p>
                <br>

                <div class='content'>
                    {$content}
                </div>

                <div class='footer'>
                    <p style='margin: 0;'><b>PUNONG BARANGAY</b></p>
                    <p style='margin: 0; font-size: 11pt;'>Punong Barangay</p>
                </div>
            </body>
            </html>
        ";

        $fileName = Str::slug($documentRequest->document_type) . '-' . ($resident->last_name ?? 'Resident') . '.doc';

        return response($htmlContent, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Upload and automatically attach or manage signed document via admin panel.
     */
    public function adminUploadAndSign(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'admin_pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('admin_pdf');
        $originalPath = $file->store('temp_uploads', 'public');
        $fullOriginalPath = storage_path('app/public/' . $originalPath);

        $signatory = User::whereIn('role', ['captain', 'punong_barangay', 'admin'])
            ->whereNotNull('signature')
            ->latest()
            ->first();

        $sigPath = null;
        if ($signatory && $signatory->signature) {
            $possiblePath = storage_path('app/public/' . $signatory->signature);
            if (file_exists($possiblePath)) {
                $sigPath = $possiblePath;
            }
        }

        $punongBarangayName = trim(($signatory->first_name ?? '') . ' ' . ($signatory->last_name ?? ''));
        if (empty($punongBarangayName)) {
            $punongBarangayName = $signatory->name ?? 'PUNONG BARANGAY';
        }

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fullOriginalPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            if ($pageNo == $pageCount) {
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Helvetica', 'B', 10);
                
                $xSignature = 130; 
                $ySignature = 210; 

                if ($sigPath && file_exists($sigPath)) {
                    try {
                        $pdf->Image($sigPath, $xSignature, $ySignature - 15, 40, 18);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to render signature: ' . $e->getMessage());
                    }
                }

                $pdf->SetXY($xSignature - 5, $ySignature + 3);
                $pdf->Cell(50, 4, '', 'T', 0, 'C'); 

                $pdf->SetXY($xSignature - 5, $ySignature + 7);
                $pdf->Cell(50, 5, strtoupper($punongBarangayName), 0, 1, 'C');

                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->SetXY($xSignature - 5, $ySignature + 12);
                $pdf->Cell(50, 5, 'PUNONG BARANGAY', 0, 1, 'C');
            }
        }

        $filename = 'signed-' . Str::slug($documentRequest->document_type) . '-' . $documentRequest->tracking_number . '.pdf';
        $outputPath = 'completed_documents/' . $filename;
        $fullOutputPath = storage_path('app/public/' . $outputPath);

        $pdf->Output($fullOutputPath, 'F');
        Storage::disk('public')->delete($originalPath);

        $documentRequest->update([
            'status' => 'completed',
            'completed_document' => $outputPath,
        ]);

        if ($documentRequest->user) {
            $documentRequest->user->notify(new ResidentPortalNotification(
                'Document Signed & Completed',
                'Your requested document (' . $documentRequest->document_type . ') has been signed by the Punong Barangay and is now completed.',
                route('resident.documents.show', $documentRequest->id)
            ));
        }

        return back()->with('success', 'Document uploaded, signed successfully, and resident has been notified!');
    }
}
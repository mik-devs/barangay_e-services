<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\FacilityPayment;
use App\Models\DocumentRequest;
use App\Models\FacilityBooking;
use App\Services\FeeCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $feeCalculator;

    public function __construct(FeeCalculatorService $feeCalculator)
    {
        $this->feeCalculator = $feeCalculator;
    }

    public function index(Request $request)
{
    $type = $request->input('type'); 
    $userId = auth()->id();

    if ($type === 'facility') {
        $payments = FacilityPayment::where('resident_id', $userId)
            ->with('facilityBooking')
            ->latest()
            ->paginate(10)
            ->withQueryString();
    } elseif ($type === 'document') {
        $payments = Payment::where('resident_id', $userId)
            ->with('payable')
            ->latest()
            ->paginate(10)
            ->withQueryString();
    } else {
    
        $docPayments = Payment::where('resident_id', $userId)->with('payable')->get();
        $facilityPayments = FacilityPayment::where('resident_id', $userId)->with('facilityBooking')->get();

        
        $merged = $docPayments->concat($facilityPayments)->sortByDesc('created_at');

        
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $merged->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $merged->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    return view('resident.payments.index', compact('payments', 'type'));
}

    public function processPayment(Request $request, $type, $id)
    {
        $request->validate([
            'payment_method' => 'required|string|in:gcash,maya,qrph,online_banking,cash',
        ]);

        if ($type === 'facility') {
            $facilityPayment = FacilityPayment::where('resident_id', auth()->id())
                ->where('facility_booking_id', $id)
                ->firstOrFail();

            $facilityPayment->update([
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash' ? 'waiting_for_payment' : 'paid',
                'paid_at'        => $request->payment_method === 'cash' ? null : now(),
                'or_number'      => $request->payment_method === 'cash' ? null : 'OR-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'expires_at'     => now()->addHours(24),
            ]);

            return redirect()->route('resident.payments.receipt', $facilityPayment->id)
                ->with('success', 'Facility payment transaction processed successfully.');
        }

        // Default para sa document requests
        $payableModel = DocumentRequest::findOrFail($id);
        $amount = $this->feeCalculator->calculateDocumentFee($payableModel->document_type ?? 'Standard');

        $payment = Payment::create([
            'reference_number' => 'REF-' . strtoupper(Str::random(10)),
            'payable_type'     => get_class($payableModel),
            'payable_id'       => $payableModel->id,
            'resident_id'      => auth()->id(),
            'amount'           => $amount,
            'payment_method'   => $request->payment_method,
            'payment_status'   => $request->payment_method === 'cash' ? 'waiting_for_payment' : 'paid',
            'paid_at'          => $request->payment_method === 'cash' ? null : now(),
            'or_number'        => $request->payment_method === 'cash' ? null : 'OR-' . date('Y') . '-' . strtoupper(Str::random(6)),
            'expires_at'       => now()->addHours(24),
        ]);

        return redirect()->route('resident.payments.receipt', $payment->id)
            ->with('success', 'Document payment transaction initiated successfully.');
    }

    public function showReceipt($id)
    {
        // Pwede itong mag-check sa parehong tables kung saan nanggaling ang receipt ID
        $payment = Payment::find($id) ?? FacilityPayment::findOrFail($id);

        if ($payment->resident_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('payments.receipt', compact('payment'));
    }

    public function downloadPdf($id)
    {
        $payment = Payment::find($id) ?? FacilityPayment::findOrFail($id);

        if ($payment->resident_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('payments.receipt', compact('payment'));
    }
    public function checkout($type, $id)
    {
        if ($type === 'document') {
            $payableModel = DocumentRequest::findOrFail($id);
            
            $paymentRecord = Payment::where('payable_type', DocumentRequest::class)
                ->where('payable_id', $id)
                ->first();
        } else {
            $payableModel = FacilityBooking::findOrFail($id);
            
            $paymentRecord = FacilityPayment::where('resident_id', auth()->id())
                ->where('facility_booking_id', $id)
                ->firstOrFail();
        }

        return view('resident.payments.checkout', compact('payableModel', 'type', 'paymentRecord'));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $todayRevenue = Payment::where('payment_status', 'paid')->whereDate('paid_at', today())->sum('amount');
        $weeklyRevenue = Payment::where('payment_status', 'paid')->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
        $monthlyRevenue = Payment::where('payment_status', 'paid')->whereMonth('paid_at', now()->month)->sum('amount');

        $pendingPayments = Payment::where('payment_status', 'pending')->count();
        $completedPayments = Payment::where('payment_status', 'paid')->count();
        $failedTransactions = Payment::where('payment_status', 'failed')->count();
        $refundRequests = Refund::where('status', 'pending')->count();

        // Search at Filter para sa Payments Table
        $search = $request->input('search');
        $status = $request->input('status');

        $recentPayments = Payment::with(['resident', 'payable'])
            ->when($search, function ($query, $search) {
                $query->where('reference_number', 'like', "%{$search}%")
                      ->orWhere('or_number', 'like', "%{$search}%")
                      ->orWhereHas('resident', function ($subQ) use ($search) {
                          $subQ->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%");
                      });
            })
            ->when($status, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.dashboard', compact(
            'todayRevenue', 'weeklyRevenue', 'monthlyRevenue',
            'pendingPayments', 'completedPayments', 'failedTransactions',
            'refundRequests', 'recentPayments'
        ));
    }

    public function verifyOfflinePayment(Payment $payment)
    {
        $payment->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'or_number' => 'OR-' . date('Y') . '-' . strtoupper(uniqid()),
        ]);

        return back()->with('success', 'Offline payment verified and marked as paid successfully.');
    }

    public function processRefund(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        Refund::create([
            'payment_id' => $payment->id,
            'admin_id' => auth()->id(),
            'amount' => $payment->amount,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        $payment->update(['payment_status' => 'refunded']);

        return back()->with('success', 'Refund request processed successfully.');
    }
}
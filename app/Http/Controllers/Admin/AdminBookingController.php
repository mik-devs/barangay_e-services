<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityBooking;
use App\Models\FacilityPayment;
use App\Models\ActivityLog;
use App\Notifications\ResidentPortalNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class AdminBookingController extends Controller
{
    public function index(Request $request): View
    {
        auth()->user()->update(['last_read_bookings' => now()]);

        $search = $request->input('search');
        $status = $request->input('status');
        $facility = $request->input('facility', 'all');
        
        $bookings = FacilityBooking::with('user')
            ->when($search, function ($query, $search) {
                if (is_numeric($search)) {
                    $query->where('id', $search);
                } else {
                    $query->where('facility_name', 'like', "%{$search}%")
                          ->orWhere('reference_number', 'like', "%{$search}%")
                          ->orWhere('purpose', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($subQ) use ($search) {
                              $subQ->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%");
                          });
                }
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($facility !== 'all', function ($query) use ($facility) {
                $query->where('facility_name', $facility);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(FacilityBooking $facilityBooking): View
    {
        $facilityBooking->load('user', 'facilityPayment');
        return view('admin.bookings.show', compact('facilityBooking'));
    }

    public function updateStatus(Request $request, FacilityBooking $facilityBooking): RedirectResponse
    {
        $validated = $request->validate([
            'status'        => ['required', 'in:pending,approved,rejected,cancelled'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
            'amount'        => ['nullable', 'numeric', 'min:0'],
        ]);

        // 1. Update booking status, admin_remarks, at amount sa facility_bookings table
        $facilityBooking->update($validated);

        // 2. Create or update facility payment record sa hiwalay na facility_payments table
        if ($validated['status'] === 'approved' && !empty($validated['amount']) && $validated['amount'] > 0) {
            FacilityPayment::updateOrCreate(
                [
                    'facility_booking_id' => $facilityBooking->id,
                ],
                [
                    'reference_number'    => $facilityBooking->reference_number ?? 'REF-' . strtoupper(Str::random(10)),
                    'resident_id'         => $facilityBooking->user_id,
                    'amount'              => $validated['amount'],
                    'payment_status'      => 'pending',
                    'payment_method'      => 'cash',
                ]
            );
        }
        
        // 3. Log activity
        $bookerName = $facilityBooking->user ? ($facilityBooking->user->first_name . ' ' . $facilityBooking->user->last_name) : 'Unknown Resident';
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'Updated Booking Status',
            'description' => 'Updated facility booking (' . $facilityBooking->facility_name . ') made by ' . $bookerName . ' to status: ' . ucfirst($facilityBooking->status),
        ]);

        // 4. Send Notification to Resident
        if ($facilityBooking->user) {
            $statusLabels = [
                'approved'  => 'has been approved',
                'rejected'  => 'has been rejected',
                'cancelled' => 'has been cancelled',
                'pending'   => 'status has been set back to pending',
            ];

            $statusText = $statusLabels[$facilityBooking->status] ?? 'status has been updated to ' . $facilityBooking->status;

            $notificationMessage = 'Your booking for ' . $facilityBooking->facility_name . ' ' . $statusText . '.';
            if ($facilityBooking->status === 'approved' && $facilityBooking->amount > 0) {
                $notificationMessage .= ' Please settle the payment amount of ₱' . number_format($facilityBooking->amount, 2) . '.';
            }

            $facilityBooking->user->notify(new ResidentPortalNotification(
                'Facility Booking Update',
                $notificationMessage,
                route('resident.bookings.show', $facilityBooking->id)
            ));
        }

        return back()->with('success', 'Facility booking status updated, separate payment record created, and resident has been notified successfully!');
    }
}
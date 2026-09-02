<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\FacilityBooking;
use App\Models\User;
use App\Notifications\ResidentPortalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class FacilityBookingController extends Controller
{
    public function index(Request $request)
    {
        $facility = $request->get('facility', 'all');
        $query = FacilityBooking::where('user_id', auth()->id());

        if ($facility !== 'all') {
            $query->where('facility_name', $facility);
        }

        $bookings = $query->latest()->paginate(10);

        return view('resident.bookings.index', compact('bookings'));
    }

    public function create()
    {
        return view('resident.bookings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_name' => 'required|string',
            'booking_date'  => 'required|date|after_or_equal:today',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'purpose'       => 'required|string',
        ]);

        $booking = FacilityBooking::create([
            'reference_number' => 'BK-' . strtoupper(Str::random(8)),
            'user_id'          => auth()->id(),
            'facility_name'    => $validated['facility_name'],
            'booking_date'     => $validated['booking_date'],
            'start_time'       => $validated['start_time'],
            'end_time'         => $validated['end_time'],
            'purpose'          => $validated['purpose'],
            'status'           => 'pending',
        ]);

        $admins = User::whereIn('role', ['admin', 'staff'])->get();
        
        $resident = auth()->user();
        $residentName = trim(($resident->first_name ?? '') . ' ' . ($resident->last_name ?? ''));
        if (empty($residentName)) {
            $residentName = $resident->full_name ?? 'Resident';
        }

        Notification::send($admins, new ResidentPortalNotification(
            'New Facility Booking',
            $residentName . ' booked a facility (' . $booking->facility_name . ').',
            route('admin.bookings.show', $booking->id)
        ));

        return redirect()->route('resident.bookings.index')
            ->with('success', 'Facility reservation submitted successfully!');
    }

    public function show(FacilityBooking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('resident.bookings.show', compact('booking'));
    }
}
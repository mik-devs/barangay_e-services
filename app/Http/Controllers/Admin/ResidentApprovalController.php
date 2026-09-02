<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidentApprovalController extends Controller
{
    public function index(Request $request): View
    {
        auth()->user()->update(['last_read_residents' => now()]);

        $search = $request->input('search');
        $status = $request->input('status');

        $residents = User::query()
            ->with('profile')
            ->where('role', 'resident')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                if ($status === 'verified') {
                    return $query->whereIn('account_status', ['verified', 'approved']);
                }
                return $query->where('account_status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.residents.index', compact('residents'));
    }

    public function show(User $resident): View
    {
        if ($resident->role !== 'resident') {
            abort(404);
        }

        $resident->load('profile');

        return view('admin.residents.show', compact('resident'));
    }

    public function approve(Request $request, User $resident)
    {
        $resident->update([
            'account_status' => 'verified',
            'email_verified_at' => now(),
        ]);

        // Save an activity log for the approval action
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Approved Resident Account',
            'description' => 'Approved the account of ' . $resident->full_name,
        ]);

        return redirect()->route('admin.residents.index', ['status' => 'verified'])
                         ->with('success', 'Resident account has been verified successfully.');
    }

    public function reject(Request $request, User $resident)
    {
        $reason = $request->input('rejection_reason', 'Account rejected by the administrator.');

        $resident->update([
            'account_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Save an activity log for the rejection action
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Rejected Resident Account',
            'description' => 'Rejected the account of ' . $resident->full_name . '. Reason: ' . $reason,
        ]);

        return redirect()->route('admin.residents.index', ['status' => 'rejected'])
                         ->with('success', 'Resident account has been rejected.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use App\Models\ActivityLog; 
use App\Notifications\ResidentPortalNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminIncidentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $incidents = IncidentReport::with('user')
            ->when($search, function ($query, $search) {
                if (is_numeric($search)) {
                    $query->where('id', $search);
                } else {
                    $query->where('report_number', 'like', "%{$search}%")
                          ->orWhere('incident_type', 'like', "%{$search}%")
                          ->orWhere('location', 'like', "%{$search}%")
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

        return view('admin.incidents.index', compact('incidents'));
    }

    public function show(IncidentReport $incidentReport): View
    {
        $incidentReport->load('user');

        $incident = $incidentReport;
        $resident = $incidentReport->user; 

        return view('admin.incidents.show', compact('incident', 'resident'));
    }

    public function updateStatus(Request $request, IncidentReport $incidentReport): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,investigating,resolved,rejected'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $incidentReport->update($validated);
        
        $reporterName = $incidentReport->user ? ($incidentReport->user->first_name . ' ' . $incidentReport->user->last_name) : 'Unknown Resident';
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Incident Status',
            'description' => 'Updated incident report (' . $incidentReport->incident_type . ') filed by ' . $reporterName . ' to status: ' . ucfirst($validated['status']),
        ]);

        if ($incidentReport->user) {
            $statusLabels = [
                'investigating' => 'is now under investigation',
                'resolved'      => 'has been resolved',
                'rejected'      => 'has been rejected',
                'pending'       => 'status has been set back to pending',
            ];

            $statusText = $statusLabels[$incidentReport->status] ?? 'status has been updated to ' . $incidentReport->status;

            $incidentReport->user->notify(new ResidentPortalNotification(
                'Incident Report Update',
                'Your incident report regarding ' . $incidentReport->incident_type . ' ' . $statusText . '.',
                route('resident.incidents.show', $incidentReport->id)
            ));
        }

        return back()->with('success', 'Incident report status updated and resident has been notified successfully!');
    }
}
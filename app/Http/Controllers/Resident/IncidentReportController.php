<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use App\Models\User;
use App\Notifications\ResidentPortalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class IncidentReportController extends Controller
{
    // List of submitted reports
    public function index()
    {
        $reports = IncidentReport::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('resident.incidents.index', compact('reports'));
    }

    // Form page for reporting incident
    public function create()
    {
        return view('resident.incidents.create');
    }

    // Save submitted incident report
    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_type' => 'required|string',
            'location'      => 'required|string|max:255',
            'incident_date' => 'required|date',
            'description'   => 'required|string',
            'attachment'    => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('incident_attachments', 'public');
        }

        $incident = IncidentReport::create([
            'report_number' => 'INC-' . strtoupper(Str::random(8)),
            'user_id'       => auth()->id(),
            'incident_type' => $validated['incident_type'],
            'location'      => $validated['location'],
            'incident_date' => $validated['incident_date'],
            'description'   => $validated['description'],
            'attachment'    => $filePath,
            'status'        => 'pending',
        ]);

        $admins = User::whereIn('role', ['admin', 'staff'])->get();
        
        $resident = auth()->user();
        $residentName = trim(($resident->first_name ?? '') . ' ' . ($resident->last_name ?? ''));
        if (empty($residentName)) {
            $residentName = $resident->full_name ?? 'Resident';
        }

        Notification::send($admins, new ResidentPortalNotification(
            'New Incident Report',
            $residentName . ' submitted a report regarding ' . $incident->incident_type . '.',
            route('admin.incidents.show', $incident->id)
        ));

        return redirect()->route('resident.incidents.index')
            ->with('success', 'Incident report submitted successfully!');
    }

    // Show details of a specific report
    public function show(IncidentReport $incident)
    {
        if ($incident->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('resident.incidents.show', compact('incident'));
    }
}
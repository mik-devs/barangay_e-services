<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\IncidentReport;
use App\Models\FacilityBooking;
use App\Models\User;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $documentCount = DocumentRequest::count();
        $incidentCount = IncidentReport::count();
        $bookingCount = FacilityBooking::count();
        
        // Bilang ng mga residente (i-adjust ang role name kung kinakailangan, halimbawa 'resident' o 'user')
        $residentCount = User::where('role', 'resident')->count(); 
        // Kung walang role column at lahat ng users ay residente maliban sa admin/staff:
        // $residentCount = User::where('role', '!=', 'admin')->count();

        return view('admin.reports.index', compact(
            'documentCount', 
            'incidentCount', 
            'bookingCount', 
            'residentCount'
        ));
    }

    public function export(Request $request)
    {
        $documentCount = DocumentRequest::count();
        $incidentCount = IncidentReport::count();
        $bookingCount = FacilityBooking::count();
        $residentCount = User::where('role', 'resident')->count();

        $filename = "system-reports-summary-" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($documentCount, $incidentCount, $bookingCount, $residentCount) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Report Category', 'Total Count', 'Generated Date']);
            fputcsv($file, ['Document Requests', $documentCount, date('Y-m-d H:i:s')]);
            fputcsv($file, ['Incident Reports', $incidentCount, date('Y-m-d H:i:s')]);
            fputcsv($file, ['Facility Bookings', $bookingCount, date('Y-m-d H:i:s')]);
            fputcsv($file, ['Registered Residents', $residentCount, date('Y-m-d H:i:s')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
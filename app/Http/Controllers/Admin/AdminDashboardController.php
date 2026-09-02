<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\IncidentReport;
use App\Models\FacilityBooking;
use App\Models\ActivityLog;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // Counts / Statistics
        $totalResidents = User::where('role', 'resident')->count();
        $pendingRegistrations = User::where('role', 'resident')->where('account_status', 'pending')->count();
        
        $pendingDocumentRequests = DocumentRequest::where('status', 'pending')->count();
        $totalDocumentRequests = DocumentRequest::count();

        $pendingIncidentsCount = IncidentReport::where('status', 'pending')->count();
        $totalIncidentsCount = IncidentReport::count();

        $pendingBookingsCount = FacilityBooking::where('status', 'pending')->count();
        $totalBookingsCount = FacilityBooking::count();

        $recentResidents = User::where('role', 'resident')->with('profile')->latest()->take(5)->get();
        $recentDocuments = DocumentRequest::with('user')->latest()->take(5)->get();
        $recentIncidents = IncidentReport::latest()->take(5)->get();
        $recentBookings = FacilityBooking::latest()->take(5)->get();

        // 2. Kunin ang huling 10 activity logs kasama ang profile ng user
        $activityLogs = ActivityLog::with('user.profile')->latest()->take(10)->get();

        $registrationMonths = [];
        $registrationCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $registrationMonths[] = $month->format('M Y');
            
            $count = User::where('role', 'resident')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
                
            $registrationCounts[] = $count;
        }

        return view('admin.dashboard', compact(
            'totalResidents',
            'pendingRegistrations',
            'pendingDocumentRequests',
            'totalDocumentRequests',
            'pendingIncidentsCount',
            'totalIncidentsCount',
            'pendingBookingsCount',
            'totalBookingsCount',
            'recentResidents',
            'recentDocuments',
            'recentIncidents',
            'recentBookings',
            'registrationMonths',
            'registrationCounts',
            'activityLogs' 
        ));
    }
}
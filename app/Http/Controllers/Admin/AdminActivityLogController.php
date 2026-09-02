<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class AdminActivityLogController extends Controller
{
    public function index()
    {
    
        $logs = ActivityLog::with('user.profile')->latest()->paginate(15);

    
        return view('admin.activity-logs.index', compact('logs'));
    }
}
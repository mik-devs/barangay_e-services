<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(6);
        return view('resident.announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        
        if (request()->has('notification')) {
            auth()->user()->notifications()
                ->where('id', request()->get('notification'))
                ->first()?->markAsRead();
        }

        return view('resident.announcements.show', compact('announcement'));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\ResidentPortalNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $announcements = Announcement::latest()->paginate(10);

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'body'     => ['required', 'string'],
            'priority' => ['required', 'in:normal,important,urgent'],
            'image'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::create($validated);

        // Magpadala ng notification sa LAHAT ng residents
        $residents = User::where('role', 'resident')->get();
        if ($residents->count() > 0) {
            Notification::send($residents, new ResidentPortalNotification(
                'Bagong Barangay Announcement',
                'May bagong abiso: ' . $announcement->title,
                route('resident.announcements.show', $announcement->id)
            ));
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement successfully created and residents have been notified.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement): View
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'body'     => ['required', 'string'],
            'priority' => ['required', 'in:normal,important,urgent'],
            'image'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }
        
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement successfully deleted.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidentController extends Controller
{
    /**
     * Display a listing of the residents.
     */
    public function index(Request $request): View
    {
        auth()->user()->update(['last_read_residents' => now()]);

        $status = $request->input('status');
        
        $residents = User::with('residentProfile')
            ->where('role', 'resident')
            ->when($status, function ($query, $status) {
                return $query->where('account_status', $status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('residentProfile', function($profileQuery) use ($search) {
                          $profileQuery->where('purok_sitio', 'like', "%{$search}%")
                                       ->orWhere('street', 'like', "%{$search}%")
                                       ->orWhere('house_number', 'like', "%{$search}%");
                      });
                });
            })
            ->paginate(10);

        return view('admin.residents.index', compact('residents', 'status'));
    }

    /**
     * Show the form for creating a new resident.
     */
    public function create(): View
    {
        return view('admin.residents.create');
    }

    /**
     * Store a newly created resident in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:255'],
            'middle_name'    => ['nullable', 'string', 'max:255'],
            'last_name'      => ['required', 'string', 'max:255'],
            'suffix'         => ['nullable', 'string', 'max:50'],
            'email'          => ['required', 'email', 'max:255', 'unique:users'],
            'phone_number'   => ['nullable', 'string', 'max:20'],
            'account_status' => ['required', 'in:pending,verified,rejected'],
        ]);

        $validated['role'] = 'resident';
        $validated['password'] = bcrypt('password123'); 

        User::create($validated);

        return redirect()->route('admin.residents.index')
            ->with('success', 'Resident successfully registered.');
    }

    /**
     * Display the specified resident.
     */
    public function show(User $resident): View
    {
        $resident->load('residentProfile');

        return view('admin.residents.show', compact('resident'));
    }

    /**
     * Show the form for editing the specified resident.
     */
    public function edit(User $resident): View
    {
        $resident->load('residentProfile');
        return view('admin.residents.edit', ['resident' => $resident]);
    }

    /**
     * Update the specified resident in storage.
     */
    public function update(Request $request, User $resident): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:255'],
            'middle_name'    => ['nullable', 'string', 'max:255'],
            'last_name'      => ['required', 'string', 'max:255'],
            'suffix'         => ['nullable', 'string', 'max:50'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email,' . $resident->id],
            'phone_number'   => ['nullable', 'string', 'max:20'],
            'account_status' => ['required', 'in:pending,verified,rejected'],
        ]);

        $resident->update($validated);

        return redirect()->route('admin.residents.index')
            ->with('success', 'Resident information successfully updated.');
    }

    /**
     * Remove the specified resident from storage.
     */
    public function destroy(User $resident): RedirectResponse
    {
        $resident->delete();

        return redirect()->route('admin.residents.index')
            ->with('success', 'Resident successfully deleted.');
    }
}
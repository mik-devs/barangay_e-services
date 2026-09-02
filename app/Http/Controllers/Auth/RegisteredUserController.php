<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ResidentProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Step 1: Basic Account Info
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Step 2: Resident Profile
            'birth_date' => ['required', 'date', 'before:today'],
            'birth_place' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'civil_status' => ['required', 'in:Single,Married,Widowed,Separated'],
            'citizenship' => ['required', 'string', 'max:100'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'house_number' => ['required', 'string', 'max:50'],
            'street' => ['required', 'string', 'max:100'],
            'purok_sitio' => ['required', 'string', 'max:100'],
            'is_voter' => ['required', 'boolean'],
            'voter_precinct_no' => ['nullable', 'required_if:is_voter,1', 'string', 'max:50'],

            // Emergency Contact (ICE) & Medical Details
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_relation' => ['required', 'string', 'max:100'],
            'emergency_contact_number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
            'blood_type' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'], // <-- Idinagdag na validation

            // Step 4: Identity Uploads
            'id_type' => ['required', 'string', 'max:100'],
            'id_number' => ['required', 'string', 'max:100'],
            'id_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'id_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'proof_of_residency' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        DB::beginTransaction();

        $idFrontPath = null;
        $idBackPath = null;
        $proofPath = null;

        try {
            // Upload ID documents if provided
            if ($request->hasFile('id_front')) {
                $idFrontPath = $request->file('id_front')->store('uploads/ids', 'public');
            }
            if ($request->hasFile('id_back')) {
                $idBackPath = $request->file('id_back')->store('uploads/ids', 'public');
            }
            if ($request->hasFile('proof_of_residency')) {
                $proofPath = $request->file('proof_of_residency')->store('uploads/proofs', 'public');
            }

            // 1. Create User
            $user = User::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'suffix' => $validated['suffix'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'password' => Hash::make($validated['password']),
                'role' => 'resident',
                'account_status' => 'pending',
            ]);

            // 2. Create Linked Resident Profile
            ResidentProfile::create([
                'user_id' => $user->id,
                'birth_date' => $validated['birth_date'],
                'birth_place' => $validated['birth_place'],
                'gender' => $validated['gender'],
                'civil_status' => $validated['civil_status'],
                'citizenship' => $validated['citizenship'],
                'occupation' => $validated['occupation'],
                'house_number' => $validated['house_number'],
                'street' => $validated['street'],
                'purok_sitio' => $validated['purok_sitio'],
                'is_voter' => $validated['is_voter'],
                'voter_precinct_no' => $validated['voter_precinct_no'],
                // Emergency Contact Fields
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_relation' => $validated['emergency_contact_relation'],
                'emergency_contact_number' => $validated['emergency_contact_number'],
                'blood_type' => $validated['blood_type'] ?? null, 
                // Identity & Documents
                'id_type' => $validated['id_type'],
                'id_number' => $validated['id_number'],
                'id_front_path' => $idFrontPath,
                'id_back_path' => $idBackPath,
                'proof_of_residency_path' => $proofPath,
            ]);

            DB::commit();

            event(new Registered($user));

            return redirect()->route('login')->with(
                'status', 
                'Thank you for registering! Your account is currently under review by the Barangay Admin. Please wait for confirmation.'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded files if process fails
            if ($idFrontPath) Storage::disk('public')->delete($idFrontPath);
            if ($idBackPath) Storage::disk('public')->delete($idBackPath);
            if ($proofPath) Storage::disk('public')->delete($proofPath);

            return back()->withInput()->withErrors(['error' => 'An error occurred while saving: ' . $e->getMessage()]);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ResidentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $profile = $user->residentProfile ?? $user->profile;

        return view('profile.edit', [
            'user'    => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Update the user's profile information and documents.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // UPDATE USERS TABLE
        $user->first_name     = $request->input('first_name');
        $user->middle_name    = $request->input('middle_name');
        $user->last_name      = $request->input('last_name');
        $user->suffix         = $request->input('suffix');
        $user->phone_number   = $request->input('phone_number') ?? $request->input('phone');
        $user->email          = $request->input('email');
        
        
        if ($user->account_status !== 'verified') {
            $user->account_status = 'pending';
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->save();

        // UPDATE RESIDENT_PROFILES TABLE

        $profile = $user->residentProfile ?? $user->profile;

        $profileData = [
            'birth_date'        => $request->input('birth_date'),
            'birth_place'       => $request->input('birth_place'),
            'gender'            => $request->input('gender'),
            'civil_status'      => $request->input('civil_status'),
            'citizenship'       => $request->input('citizenship', 'Filipino'),
            'occupation'        => $request->input('occupation'),
            'house_number'      => $request->input('house_number'),
            'street'            => $request->input('street'),
            'purok_sitio'       => $request->input('purok_sitio'),
            'is_voter'          => $request->has('is_voter') ? (bool)$request->input('is_voter') : 1,
            'voter_precinct_no' => $request->input('voter_precinct_no'),
            'id_type'           => $request->input('id_type'),
            'id_number'         => $request->input('id_number'),
            
            'blood_type'                 => $request->input('blood_type'),
            'emergency_contact_name'     => $request->input('emergency_contact_name'),
            'emergency_contact_number'   => $request->input('emergency_contact_number'),
            'emergency_contact_relation' => $request->input('emergency_contact_relation'),
        ];

        // Valid ID - Front Upload
        if ($request->hasFile('id_front_path')) {
            if ($profile && $profile->id_front_path) {
                Storage::disk('public')->delete($profile->id_front_path);
            }
            $profileData['id_front_path'] = $request->file('id_front_path')->store('uploads/ids', 'public');
        }

        // Valid ID - Back Upload
        if ($request->hasFile('id_back_path')) {
            if ($profile && $profile->id_back_path) {
                Storage::disk('public')->delete($profile->id_back_path);
            }
            $profileData['id_back_path'] = $request->file('id_back_path')->store('uploads/ids', 'public');
        }

        // Proof of Residency Upload
        if ($request->hasFile('proof_of_residency_path')) {
            if ($profile && $profile->proof_of_residency_path) {
                Storage::disk('public')->delete($profile->proof_of_residency_path);
            }
            $profileData['proof_of_residency_path'] = $request->file('proof_of_residency_path')->store('uploads/proofs', 'public');
        }

        // Save or Update Record
        ResidentProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $profile = $user->residentProfile ?? $user->profile;
        if ($profile) {
            if ($profile->id_front_path) Storage::disk('public')->delete($profile->id_front_path);
            if ($profile->id_back_path) Storage::disk('public')->delete($profile->id_back_path);
            if ($profile->proof_of_residency_path) Storage::disk('public')->delete($profile->proof_of_residency_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
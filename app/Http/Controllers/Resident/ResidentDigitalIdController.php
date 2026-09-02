<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidentDigitalIdController extends Controller
{
    public function show(Request $request): View
    {
        $user = auth()->user();
        
        $profile = $user->residentProfile()->with('emergencyContacts')->first();

        if (!$profile) {
            return view('resident.digital-id.incomplete');
        }

        $verificationUrl = route('public.verify-document', $profile->id_number ?? $user->id);

        return view('resident.digital-id.show', compact('user', 'profile', 'verificationUrl'));
    }
}
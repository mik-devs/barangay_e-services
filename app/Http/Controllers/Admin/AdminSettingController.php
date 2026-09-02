<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class AdminSettingController extends Controller
{
    public function index()
    {
        // Kunin ang unang row ng settings, o gumawa ng default kung wala pa
        $settings = Setting::first();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'barangay_name' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        // Hanapin ang setting o gumawa ng bago kung wala pa (First or Create)
        $settings = Setting::first();

        if ($settings) {
            $settings->update($request->all());
        } else {
            Setting::create($request->all());
        }

        return redirect()->route('admin.settings.index')->with('success', 'System settings updated successfully.');
    }
}
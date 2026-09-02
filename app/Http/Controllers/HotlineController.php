<?php

namespace App\Http\Controllers;

use App\Models\Hotline;
use Illuminate\Http\Request;

class HotlineController extends Controller
{
    public function index()
    {
        // Kunin lahat ng hotlines mula sa database
        $hotlines = Hotline::all();

        // I-pass ito papunta sa view
        return view('hotlines.index', compact('hotlines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agency_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        Hotline::create($request->all());

        return redirect()->back()->with('success', 'Hotline added successfully!');
    }
}
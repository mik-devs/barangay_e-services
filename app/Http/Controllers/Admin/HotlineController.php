<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotline; 

class HotlineController extends Controller
{
    public function index()
    {
        $hotlines = Hotline::latest()->paginate(10);
        return view('admin.hotlines.index', compact('hotlines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agency_name'    => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'description'    => 'nullable|string|max:500',
        ]);

        Hotline::create($request->all());

        return redirect()->route('admin.hotlines.index')->with('success', 'Hotline added successfully.');
    }
    public function update(Request $request, Hotline $hotline)
{
    $request->validate([
        'agency_name' => 'required|string|max:255',
        'contact_number' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
    ]);

    $hotline->update([
        'agency_name' => $request->agency_name,
        'contact_number' => $request->contact_number,
        'description' => $request->description,
    ]);

    return redirect()->route('admin.hotlines.index')->with('success', 'Hotline updated successfully.');
}
    public function destroy(Hotline $hotline)
    {
        $hotline->delete();

        return redirect()->route('admin.hotlines.index')->with('success', 'Hotline deleted successfully.');
    }
}
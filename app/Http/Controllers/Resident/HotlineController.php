<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotline; 

class HotlineController extends Controller
{
    public function index()
    {
    
        $hotlines = Hotline::all();

        
        return view('resident.hotlines.index', compact('hotlines'));
    }
}
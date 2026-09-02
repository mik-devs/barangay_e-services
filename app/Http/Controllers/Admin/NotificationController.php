<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read($id)
    {
    
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            
            $notification->markAsRead();

            
            $url = $notification->data['url'] ?? route('admin.dashboard');
            
            return redirect($url);
        }

        return redirect()->back();
    }
}
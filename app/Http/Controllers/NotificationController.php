<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead(); 
            
           
            $targetUrl = $notification->data['url'] ?? $notification->data['link'] ?? route('resident.documents.index');
            
            return redirect($targetUrl);
        }

        return back();
    }
    
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}
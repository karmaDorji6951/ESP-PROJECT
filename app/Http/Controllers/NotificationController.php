<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }
    
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return back()->with('success', 'Notification marked as read.');
    }
    
    public function markAllAsRead(Request $request)
    {
        // Handle both GET and POST requests for compatibility
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        } else {
            return back()->with('success', 'All notifications marked as read.');
        }
    }
}

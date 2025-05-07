<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        return Inertia::render('Notifications/Index', [
            'notifications' => $user->notifications()
                ->paginate(10)
                ->through(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => class_basename($notification->type),
                        'data' => $notification->data,
                        'read_at' => $notification->read_at ? $notification->read_at->diffForHumans() : null,
                        'created_at' => $notification->created_at->diffForHumans(),
                    ];
                }),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }

    public function destroy($id)
    {
        auth()->user()->notifications()->findOrFail($id)->delete();

        return back();
    }
} 
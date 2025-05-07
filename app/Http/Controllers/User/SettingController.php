<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        return Inertia::render('User/Settings/Index', [
            'settings' => [
                'profile' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                ],
                'notifications' => [
                    'email_notifications' => $user->email_notifications ?? true,
                    'push_notifications' => $user->push_notifications ?? false,
                ],
                'preferences' => [
                    'language' => $user->language ?? 'id',
                    'timezone' => $user->timezone ?? config('app.timezone'),
                ],
                'security' => [
                    'two_factor_enabled' => $user->two_factor_enabled ?? false,
                    'last_password_change' => $user->password_changed_at?->diffForHumans(),
                ],
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'notifications.email_notifications' => 'boolean',
            'notifications.push_notifications' => 'boolean',
            'preferences.language' => 'string|in:id,en',
            'preferences.timezone' => 'string|timezone',
        ]);

        $user = auth()->user();
        
        $user->update([
            'email_notifications' => $validated['notifications']['email_notifications'],
            'push_notifications' => $validated['notifications']['push_notifications'],
            'language' => $validated['preferences']['language'],
            'timezone' => $validated['preferences']['timezone'],
        ]);

        return back()->with('message', 'Settings updated successfully.');
    }
}

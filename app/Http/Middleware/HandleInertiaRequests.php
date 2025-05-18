<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;
use App\Models\Setting;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $notifications = [];

        if ($user) {
            $isAdmin = $user->hasRole('admin');
            $notifications = $user->notifications()
                ->when($isAdmin, function($query) {
                    return $query->where('type', 'App\\Notifications\\DocumentSubmitted');
                })
                ->when(!$isAdmin, function($query) {
                    return $query->where('type', '!=', 'App\\Notifications\\DocumentSubmitted');
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Log untuk debugging
        $currentRoute = $request->route() ? $request->route()->getName() : 'unknown';
        \Log::info("HandleInertiaRequests sharing data for route: {$currentRoute}");
        
        // Pastikan settings tersedia untuk semua halaman
        $settings = \App\Models\Setting::all()->mapWithKeys(function ($setting) {
            $value = $setting->value;
            
            // Handle media items
            if (in_array($setting->key, ['logo', 'favicon', 'thumbnail'])) {
                $value = $setting->getFirstMediaUrl($setting->key);
            }
            
            return [$setting->key => $value];
        });
        
        \Log::info('Settings in HandleInertiaRequests:', $settings->toArray());

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'can' => [
                'login' => fn () => !$request->user(),
                'register' => fn () => !$request->user(),
            ],
            'notifications' => $notifications,
            // Tambahkan settings langsung di sini untuk memastikan tersedia
            'settings' => $settings,
        ]);
    }
}

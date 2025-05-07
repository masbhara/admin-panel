<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index(): Response
    {
        // Log untuk debugging
        \Log::info('SettingController index method called');
        
        $flash = session()->has('message') ? [
            'message' => session('message'),
            'type' => session('type', 'success')
        ] : null;

        // Hapus flash dari session agar tidak dikirim ulang
        session()->forget('message');
        session()->forget('type');

        return Inertia::render('Admin/Settings/Index', [
            'auth' => [
                'user' => auth()->user(),
            ],
            'flash' => $flash,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Settings/Create', [
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings',
            'value' => 'required',
            'type' => 'required|string|in:text,textarea,boolean,number,select',
            'description' => 'nullable|string',
        ]);

        Setting::create($validated);

        return redirect()->route('admin.settings.index')
            ->with('message', 'Setting berhasil dibuat.')
            ->with('type', 'success');
    }

    public function show(Setting $setting): Response
    {
        return Inertia::render('Admin/Settings/Show', [
            'setting' => $setting,
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }

    public function edit(Setting $setting): Response
    {
        return Inertia::render('Admin/Settings/Edit', [
            'setting' => $setting,
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'value' => 'required',
            'description' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('message', 'Setting berhasil diperbarui.')
            ->with('type', 'success');
    }

    public function updateGeneral(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'site_title' => 'required|string|max:255',
                'site_subtitle' => 'nullable|string|max:255',
                'site_description' => 'nullable|string',
                'footer_copyright' => 'nullable|string',
                'meta_pixel' => 'nullable|string',
                'google_analytics' => 'nullable|string',
                'tiktok_pixel' => 'nullable|string',
                'twitter_pixel' => 'nullable|string',
                'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'favicon' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
                'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Handle media files
            $mediaFields = ['logo', 'favicon', 'thumbnail'];
            foreach ($mediaFields as $field) {
                if ($request->hasFile($field)) {
                    $setting = Setting::firstOrCreate(
                        ['key' => $field],
                        [
                            'group' => 'general',
                            'type' => 'file',
                            'is_public' => true
                        ]
                    );

                    // Clear existing media
                    $setting->clearMediaCollection($field);
                    
                    // Add new media
                    $setting->addMediaFromRequest($field)
                        ->toMediaCollection($field);

                    // Log media update
                    \Log::info("Media updated for {$field}");
                }
            }

            // Handle other settings
            foreach ($validated as $key => $value) {
                if (!in_array($key, $mediaFields)) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $value,
                            'group' => 'general',
                            'type' => in_array($key, ['site_description', 'meta_pixel', 'google_analytics', 'tiktok_pixel', 'twitter_pixel']) 
                                ? 'textarea' 
                                : 'text',
                            'is_public' => true
                        ]
                    );

                    \Log::info("Setting updated: {$key} = {$value}");
                }
            }

            DB::commit();
            
            // Clear all relevant caches
            $this->clearSettingsCache();
            
            // Broadcast settings update
            broadcast(new \App\Events\SettingsUpdated(Setting::all()->mapWithKeys(function ($setting) {
                $value = $setting->value;
                if (in_array($setting->key, ['logo', 'favicon', 'thumbnail'])) {
                    $value = $setting->getFirstMediaUrl($setting->key);
                }
                return [$setting->key => $value];
            })))->toOthers();

            return redirect()->back()
                ->with('message', 'Pengaturan berhasil diperbarui.')
                ->with('type', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('message', 'Terjadi kesalahan: ' . $e->getMessage())
                ->with('type', 'error');
        }
    }

    private function clearSettingsCache()
    {
        $cacheKeys = ['settings', 'app_settings', 'general_settings'];
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        \Log::info('Settings cache cleared');
    }

    public function destroy(Setting $setting)
    {
        try {
            if (in_array($setting->key, ['logo', 'favicon', 'thumbnail'])) {
                $setting->clearMediaCollection($setting->key);
            }
            
            $setting->delete();

            return redirect()->route('admin.settings.index')
                ->with('message', 'Setting berhasil dihapus.')
                ->with('type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Terjadi kesalahan: ' . $e->getMessage())
                ->with('type', 'error');
        }
    }
}

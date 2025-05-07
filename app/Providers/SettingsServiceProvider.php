<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class SettingsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('settings', function ($app) {
            return Cache::rememberForever('settings', function () {
                return Setting::all()->mapWithKeys(function ($setting) {
                    $value = $setting->value;
                    
                    // Handle media items
                    if (in_array($setting->key, ['logo', 'favicon', 'thumbnail'])) {
                        $value = $setting->getFirstMediaUrl($setting->key);
                    }
                    
                    return [$setting->key => $value];
                });
            });
        });
    }

    public function boot()
    {
        // Share settings with all views
        View::composer('*', function ($view) {
            $view->with('settings', app('settings'));
        });
    }
}

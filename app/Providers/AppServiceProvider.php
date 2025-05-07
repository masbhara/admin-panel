<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan settings ke semua halaman Inertia
        Inertia::share([
            'settings' => function () {
                // Tambahkan logging untuk debugging
                \Log::info('Sharing settings to Inertia');
                
                return Cache::remember('app_settings', 3600, function () {
                    $settings = Setting::all()->mapWithKeys(function ($setting) {
                        $value = $setting->value;
                        
                        // Handle media items
                        if (in_array($setting->key, ['logo', 'favicon', 'thumbnail'])) {
                            $value = $setting->getFirstMediaUrl($setting->key);
                            \Log::info("Media URL for {$setting->key}: {$value}");
                        }
                        
                        return [$setting->key => $value];
                    });

                    \Log::info('Settings loaded from database:', $settings->toArray());
                    return $settings;
                });
            },
        ]);

        // Register policies manually
        Gate::define('viewAny', function ($user, $model) {
            $className = class_basename($model);
            if ($className === 'Role') {
                return $user->can('view roles');
            } elseif ($className === 'Permission') {
                return $user->can('view permissions');
            }
            return false;
        });

        Gate::define('create', function ($user, $model) {
            $className = class_basename($model);
            if ($className === 'Role') {
                return $user->can('create roles');
            } elseif ($className === 'Permission') {
                return $user->can('create permissions');
            }
            return false;
        });

        Gate::define('update', function ($user, $model) {
            $className = get_class($model);
            if ($className === 'Spatie\\Permission\\Models\\Role') {
                return $user->can('edit roles');
            } elseif ($className === 'Spatie\\Permission\\Models\\Permission') {
                return $user->can('edit permissions');
            }
            return false;
        });

        Gate::define('delete', function ($user, $model) {
            $className = get_class($model);
            if ($className === 'Spatie\\Permission\\Models\\Role') {
                return $user->can('delete roles');
            } elseif ($className === 'Spatie\\Permission\\Models\\Permission') {
                return $user->can('delete permissions');
            }
            return false;
        });
    }
}

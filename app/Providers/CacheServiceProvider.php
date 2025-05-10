<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CacheServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Set default cache TTL
        Config::set('cache.ttl', env('REDIS_CACHE_TTL', 3600));

        // Cache tags untuk berbagai fitur
        if (Config::get('cache.default') === 'redis') {
            Cache::tags(['users', 'posts', 'media'])->remember('cache_version', 86400, function () {
                return now()->timestamp;
            });
        }

        // Cache configuration untuk development
        if (app()->environment('local')) {
            Config::set('cache.default', env('CACHE_DRIVER', 'file'));
            Config::set('session.driver', env('SESSION_DRIVER', 'file'));
        }

        // Cache configuration untuk production
        if (app()->environment('production')) {
            Config::set('cache.default', 'redis');
            Config::set('session.driver', 'redis');
            Config::set('queue.default', 'redis');
        }
    }
} 
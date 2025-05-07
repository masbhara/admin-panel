<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Cache;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget('settings');
        });

        static::deleted(function ($setting) {
            Cache::forget('settings');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value', 'group', 'type', 'is_public'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->useDisk('media')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);
            
        $this->addMediaCollection('favicon')
            ->singleFile()
            ->useDisk('media')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/x-icon']);
            
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->useDisk('media')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function getFirstMediaUrl($collectionName)
    {
        $media = $this->getFirstMedia($collectionName);
        if (!$media) return null;
        
        // Pastikan URL selalu absolute
        return url($media->getUrl());
    }
}

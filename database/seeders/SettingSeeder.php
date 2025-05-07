<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_title',
                'value' => 'Panel LaraVue',
                'group' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'site_subtitle',
                'value' => 'Admin Panel with Laravel and Vue.js',
                'group' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'site_description',
                'value' => 'Modern admin panel built with Laravel, Vue.js, and Tailwind CSS',
                'group' => 'general',
                'type' => 'textarea',
            ],
            [
                'key' => 'footer_copyright',
                'value' => '© ' . date('Y') . ' Panel LaraVue. All rights reserved.',
                'group' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'logo',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
            ],
            [
                'key' => 'thumbnail',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
            ],
            [
                'key' => 'meta_pixel',
                'value' => null,
                'group' => 'general',
                'type' => 'textarea',
            ],
            [
                'key' => 'google_analytics',
                'value' => null,
                'group' => 'general',
                'type' => 'textarea',
            ],
            [
                'key' => 'tiktok_pixel',
                'value' => null,
                'group' => 'general',
                'type' => 'textarea',
            ],
            [
                'key' => 'twitter_pixel',
                'value' => null,
                'group' => 'general',
                'type' => 'textarea',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

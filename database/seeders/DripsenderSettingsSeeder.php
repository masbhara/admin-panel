<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class DripsenderSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'dripsender_api_key',
                'value' => 'f75acb01-32d8-4448-8086-4aedce9f1d6f', // Default API Key
                'group' => 'notification',
                'type' => 'text',
                'is_public' => false,
            ],
            [
                'key' => 'dripsender_webhook_url',
                'value' => 'https://adam.dripsender.id:14773/api/integration/bcf0aad8-c7ca-4d61-b9a1-8993a3b38243', // Default Webhook URL
                'group' => 'notification',
                'type' => 'text',
                'is_public' => false,
            ],
            [
                'key' => 'whatsapp_notification_enabled',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'is_public' => false,
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
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            UsersTableSeeder::class,
            DocumentSeeder::class,
            DripsenderSettingsSeeder::class,
            WhatsappNotificationSeeder::class,
        ]);
    }
}

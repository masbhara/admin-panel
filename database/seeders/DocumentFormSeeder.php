<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentFormSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user pertama (super admin)
        $user = DB::table('users')->first();
        
        if (!$user) {
            throw new \Exception('No user found. Please run UserSeeder first.');
        }

        // Buat form dokumen default
        DB::table('document_forms')->insert([
            'title' => 'Form Dokumen Default',
            'description' => 'Form dokumen default untuk dokumen lama',
            'slug' => 'default-form',
            'is_active' => true,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
} 
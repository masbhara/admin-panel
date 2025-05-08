<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat atau dapatkan role admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Buat user admin jika belum ada
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        
        // Berikan role admin ke user
        $admin->assignRole($adminRole);

        // Buat beberapa dokumen contoh
        $documents = [
            [
                'name' => 'Dokumen Contoh 1',
                'description' => 'Ini adalah dokumen contoh pertama',
                'file_path' => 'documents/sample1.pdf',
                'file_name' => 'sample1.pdf',
                'file_size' => 1024 * 100, // 100 KB
                'file_type' => 'application/pdf',
                'user_id' => $admin->id,
                'uploaded_at' => Carbon::now(),
                'status' => 'pending',
                'metadata' => json_encode(['category' => 'contoh', 'tags' => ['pdf', 'contoh']]),
            ],
            [
                'name' => 'Dokumen Contoh 2',
                'description' => 'Ini adalah dokumen contoh kedua',
                'file_path' => 'documents/sample2.docx',
                'file_name' => 'sample2.docx',
                'file_size' => 1024 * 200, // 200 KB
                'file_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'user_id' => $admin->id,
                'uploaded_at' => Carbon::now()->subDays(1),
                'status' => 'approved',
                'metadata' => json_encode(['category' => 'dokumen', 'tags' => ['word', 'contoh']]),
            ],
        ];

        foreach ($documents as $doc) {
            Document::firstOrCreate(
                ['name' => $doc['name']],
                $doc
            );
        }
    }
}

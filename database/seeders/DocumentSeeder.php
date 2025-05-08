<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user admin
        $admin = User::role('admin')->first();
        
        if (!$admin) {
            return;
        }
        
        // Buat 5 dokumen contoh
        $documents = [
            [
                'name' => 'Panduan Pengguna Aplikasi',
                'description' => 'Dokumen panduan untuk membantu pengguna menggunakan aplikasi',
                'file_path' => 'documents/dummy.pdf',
                'file_name' => 'panduan_pengguna.pdf',
                'file_size' => 1024 * 500, // 500 KB
                'file_type' => 'application/pdf',
                'user_id' => $admin->id,
                'uploaded_at' => Carbon::now()->subDays(5),
                'status' => 'approved',
                'metadata' => [
                    'versi' => '1.0',
                    'penulis' => 'Tim Pengembang',
                    'kategori' => 'Panduan'
                ],
            ],
            [
                'name' => 'Template Laporan Bulanan',
                'description' => 'Template untuk pembuatan laporan bulanan',
                'file_path' => 'documents/dummy.docx',
                'file_name' => 'template_laporan.docx',
                'file_size' => 1024 * 800, // 800 KB
                'file_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'user_id' => $admin->id,
                'uploaded_at' => Carbon::now()->subDays(4),
                'status' => 'approved',
                'metadata' => [
                    'versi' => '2.1',
                    'kategori' => 'Template'
                ],
            ],
            [
                'name' => 'Data Statistik 2023',
                'description' => 'Data statistik pengguna tahun 2023',
                'file_path' => 'documents/dummy.xlsx',
                'file_name' => 'statistik_2023.xlsx',
                'file_size' => 1024 * 1200, // 1.2 MB
                'file_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'user_id' => $admin->id,
                'uploaded_at' => Carbon::now()->subDays(3),
                'status' => 'pending',
                'metadata' => [
                    'periode' => '2023',
                    'sumber' => 'Departemen Statistik'
                ],
            ],
            [
                'name' => 'Presentasi Perusahaan',
                'description' => 'Presentasi profil perusahaan untuk klien',
                'file_path' => 'documents/dummy.pptx',
                'file_name' => 'presentasi_perusahaan.pptx',
                'file_size' => 1024 * 3500, // 3.5 MB
                'file_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'user_id' => $admin->id,
                'uploaded_at' => Carbon::now()->subDays(2),
                'status' => 'approved',
                'metadata' => [
                    'versi' => '3.0',
                    'departemen' => 'Marketing'
                ],
            ],
            [
                'name' => 'Rencana Strategis 2024',
                'description' => 'Dokumen rencana strategis perusahaan tahun 2024',
                'file_path' => 'documents/dummy.pdf',
                'file_name' => 'rencana_strategis_2024.pdf',
                'file_size' => 1024 * 1800, // 1.8 MB
                'file_type' => 'application/pdf',
                'user_id' => $admin->id,
                'uploaded_at' => Carbon::now()->subDays(1),
                'status' => 'pending',
                'metadata' => [
                    'periode' => '2024',
                    'jenis' => 'Rahasia',
                    'departemen' => 'Manajemen'
                ],
            ],
        ];
        
        foreach ($documents as $doc) {
            Document::create($doc);
        }
    }
}

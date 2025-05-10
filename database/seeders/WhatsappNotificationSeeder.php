<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsappNotification;

class WhatsappNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Notifikasi Dokumen Diunggah',
                'template' => "Halo {{name}},\n\nTerima kasih telah mengunggah dokumen {{file_name}} pada {{uploaded_at}}.\n\nDokumen Anda saat ini dalam status {{status}}. Kami akan segera memprosesnya.\n\nTerima kasih,\nTim Admin",
                'event_type' => 'document_uploaded',
                'is_active' => true,
                'variables' => ['name', 'file_name', 'status', 'uploaded_at'],
            ],
            [
                'name' => 'Notifikasi Dokumen Disetujui',
                'template' => "Halo {{name}},\n\nDokumen Anda {{file_name}} telah DISETUJUI.\n\nTerima kasih telah menggunakan layanan kami.\n\nSalam,\nTim Admin",
                'event_type' => 'document_approved',
                'is_active' => true,
                'variables' => ['name', 'file_name'],
            ],
            [
                'name' => 'Notifikasi Dokumen Ditolak',
                'template' => "Halo {{name}},\n\nMohon maaf, dokumen Anda {{file_name}} TIDAK DISETUJUI.\n\nSilakan hubungi kami untuk informasi lebih lanjut.\n\nSalam,\nTim Admin",
                'event_type' => 'document_rejected',
                'is_active' => true,
                'variables' => ['name', 'file_name'],
            ],
        ];

        foreach ($templates as $template) {
            WhatsappNotification::updateOrCreate(
                [
                    'name' => $template['name'],
                    'event_type' => $template['event_type']
                ],
                $template
            );
        }
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentFormNotificationSetting;
use App\Models\DocumentForm;

class DocumentFormNotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultTemplates = [
            'document_uploaded' => [
                'name' => 'Notifikasi Dokumen Diunggah',
                'template' => "Halo {{name}},\n\nTerima kasih telah mengunggah dokumen {{file_name}} pada {{uploaded_at}}.\n\nDokumen Anda saat ini dalam status {{status}}. Kami akan segera memprosesnya.\n\nTerima kasih,\nTim Admin",
                'variables' => ['name', 'file_name', 'status', 'uploaded_at'],
            ],
            'document_approved' => [
                'name' => 'Notifikasi Dokumen Disetujui',
                'template' => "Halo {{name}},\n\nDokumen Anda {{file_name}} telah DISETUJUI.\n\nTerima kasih telah menggunakan layanan kami.\n\nSalam,\nTim Admin",
                'variables' => ['name', 'file_name'],
            ],
            'document_rejected' => [
                'name' => 'Notifikasi Dokumen Ditolak',
                'template' => "Halo {{name}},\n\nMohon maaf, dokumen Anda {{file_name}} TIDAK DISETUJUI.\n\nSilakan hubungi kami untuk informasi lebih lanjut.\n\nSalam,\nTim Admin",
                'variables' => ['name', 'file_name'],
            ],
        ];

        // Buat pengaturan notifikasi untuk setiap form yang ada
        DocumentForm::all()->each(function ($form) use ($defaultTemplates) {
            DocumentFormNotificationSetting::updateOrCreate(
                ['document_form_id' => $form->id],
                [
                    'whatsapp_notification_enabled' => true,
                    'notification_templates' => $defaultTemplates,
                ]
            );
        });
    }
} 
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentForm;
use App\Models\DocumentFormNotificationSetting;
use App\Services\DripsenderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class DocumentFormNotificationController extends Controller
{
    protected $dripsenderService;

    public function __construct(DripsenderService $dripsenderService)
    {
        $this->dripsenderService = $dripsenderService;
    }

    /**
     * Show notification settings for a document form
     */
    public function show(DocumentForm $documentForm)
    {
        // Hanya perlu log sederhana seperti EmptyPageController
        Log::info('DocumentFormNotificationController@show - Loading notification settings page');
        
        $notificationSettings = DocumentFormNotificationSetting::where('document_form_id', $documentForm->id)
            ->first();
            
        if (!$notificationSettings) {
            $notificationSettings = new DocumentFormNotificationSetting();
            $notificationSettings->document_form_id = $documentForm->id;
            $notificationSettings->whatsapp_notification_enabled = false;
            $notificationSettings->dripsender_api_key = '';
            $notificationSettings->dripsender_webhook_url = '';
            
            // Default templates
            $notificationSettings->notification_templates = [
                'document_uploaded' => [
                    'name' => 'Notifikasi Dokumen Diunggah',
                    'template' => 'Halo {name}, dokumen Anda "{file_name}" telah berhasil diunggah pada {uploaded_at}.',
                    'variables' => ['name', 'file_name', 'uploaded_at']
                ],
                'document_approved' => [
                    'name' => 'Notifikasi Dokumen Disetujui',
                    'template' => 'Halo {name}, dokumen Anda "{file_name}" telah disetujui.',
                    'variables' => ['name', 'file_name', 'status']
                ],
                'document_rejected' => [
                    'name' => 'Notifikasi Dokumen Ditolak',
                    'template' => 'Halo {name}, dokumen Anda "{file_name}" ditolak. Silakan unggah ulang dokumen yang sesuai.',
                    'variables' => ['name', 'file_name', 'status']
                ]
            ];
        }

        return Inertia::render('Admin/DocumentFormNotifications/Show', [
            'documentForm' => $documentForm,
            'notificationSettings' => $notificationSettings,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    /**
     * Update notification settings for a document form
     */
    public function update(Request $request, DocumentForm $documentForm)
    {
        try {
            $notificationSettings = DocumentFormNotificationSetting::firstOrCreate(
                ['document_form_id' => $documentForm->id],
                [
                    'whatsapp_notification_enabled' => false,
                    'dripsender_api_key' => null,
                    'dripsender_webhook_url' => null,
                    'notification_templates' => []
                ]
            );

            // Update basic settings
            if ($request->has('whatsapp_notification_enabled')) {
                $notificationSettings->whatsapp_notification_enabled = $request->whatsapp_notification_enabled;
            }
            
            if ($request->has('dripsender_api_key')) {
                $notificationSettings->dripsender_api_key = $request->dripsender_api_key;
            }
            
            if ($request->has('dripsender_webhook_url')) {
                $notificationSettings->dripsender_webhook_url = $request->dripsender_webhook_url;
            }
            
            // Update notification templates
            if ($request->has('notification_templates')) {
                $notificationSettings->notification_templates = $request->notification_templates;
            }

            $notificationSettings->save();

            return back()->with('success', 'Pengaturan notifikasi WhatsApp berhasil disimpan.');
            
        } catch (\Exception $e) {
            Log::error('Error updating notification settings: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Test connection with Dripsender API
     */
    public function testConnection(Request $request, DocumentForm $documentForm)
    {
        try {
            $apiKey = $request->input('api_key');
            
            if (empty($apiKey)) {
                return back()->with('error', 'API Key tidak boleh kosong');
            }
            
            $this->dripsenderService->setApiKey($apiKey);
            $response = $this->dripsenderService->testConnection();
            
            if ($response['success']) {
                return back()->with('success', 'Koneksi ke Dripsender berhasil.');
            }
            
            return back()->with('error', 'Koneksi ke Dripsender gagal: ' . ($response['message'] ?? 'Unknown error'));
            
        } catch (\Exception $e) {
            Log::error('Error testing Dripsender connection: ' . $e->getMessage());
            return back()->with('error', 'Koneksi ke Dripsender gagal: ' . $e->getMessage());
        }
    }
} 
<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentFormNotificationSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class WhatsappNotificationService
{
    protected $dripsenderService;

    public function __construct(DripsenderService $dripsenderService)
    {
        $this->dripsenderService = $dripsenderService;
    }

    /**
     * Get notification settings for document form
     */
    protected function getFormNotificationSettings(Document $document): ?DocumentFormNotificationSetting
    {
        return DocumentFormNotificationSetting::where('document_form_id', $document->document_form_id)
            ->where('whatsapp_notification_enabled', true)
            ->first();
    }

    /**
     * Send notification for document upload
     */
    public function sendDocumentUploadNotification(Document $document): array
    {
        // Get form notification settings
        $settings = $this->getFormNotificationSettings($document);
        if (!$settings) {
            return [
                'success' => false,
                'message' => 'Notifikasi WhatsApp tidak diaktifkan untuk form ini'
            ];
        }

        // Check if document has WhatsApp number
        if (empty($document->whatsapp_number)) {
            return [
                'success' => false,
                'message' => 'Dokumen tidak memiliki nomor WhatsApp'
            ];
        }

        // Check if notification already sent
        if ($document->notification_sent) {
            return [
                'success' => false,
                'message' => 'Notifikasi sudah dikirim sebelumnya'
            ];
        }

        // Get notification template
        $template = $settings->getTemplate('document_uploaded');
        if (!$template) {
            return [
                'success' => false,
                'message' => 'Template notifikasi tidak ditemukan'
            ];
        }

        // Prepare data for template parsing
        $data = [
            'name' => $document->name,
            'file_name' => $document->file_name,
            'status' => $document->status,
            'uploaded_at' => $document->uploaded_at->format('d-m-Y H:i:s'),
        ];

        // Parse template
        $message = $this->parseTemplate($template['template'], $data);

        // Format phone number (ensure it starts with 62)
        $phone = $this->formatPhoneNumber($document->whatsapp_number);

        // Send WhatsApp notification using form's API key
        $this->dripsenderService->setApiKey($settings->dripsender_api_key);
        $result = $this->dripsenderService->sendMessage($phone, $message);

        if ($result['success']) {
            // Update document notification status
            $document->notification_sent = true;
            $document->save();
        }

        return $result;
    }

    /**
     * Send notification for document approval
     */
    public function sendDocumentApprovalNotification(Document $document): array
    {
        // Get form notification settings
        $settings = $this->getFormNotificationSettings($document);
        if (!$settings) {
            return [
                'success' => false,
                'message' => 'Notifikasi WhatsApp tidak diaktifkan untuk form ini'
            ];
        }

        // Check if document has WhatsApp number
        if (empty($document->whatsapp_number)) {
            return [
                'success' => false,
                'message' => 'Dokumen tidak memiliki nomor WhatsApp'
            ];
        }

        // Get notification template
        $template = $settings->getTemplate('document_approved');
        if (!$template) {
            return [
                'success' => false,
                'message' => 'Template notifikasi tidak ditemukan'
            ];
        }

        // Prepare data for template parsing
        $data = [
            'name' => $document->name,
            'file_name' => $document->file_name,
            'status' => $document->status,
            'uploaded_at' => $document->uploaded_at->format('d-m-Y H:i:s'),
        ];

        // Parse template
        $message = $this->parseTemplate($template['template'], $data);

        // Format phone number (ensure it starts with 62)
        $phone = $this->formatPhoneNumber($document->whatsapp_number);

        // Send WhatsApp notification using form's API key
        $this->dripsenderService->setApiKey($settings->dripsender_api_key);
        return $this->dripsenderService->sendMessage($phone, $message);
    }

    /**
     * Send notification for document rejection
     */
    public function sendDocumentRejectionNotification(Document $document): array
    {
        // Get form notification settings
        $settings = $this->getFormNotificationSettings($document);
        if (!$settings) {
            return [
                'success' => false,
                'message' => 'Notifikasi WhatsApp tidak diaktifkan untuk form ini'
            ];
        }

        // Check if document has WhatsApp number
        if (empty($document->whatsapp_number)) {
            return [
                'success' => false,
                'message' => 'Dokumen tidak memiliki nomor WhatsApp'
            ];
        }

        // Get notification template
        $template = $settings->getTemplate('document_rejected');
        if (!$template) {
            return [
                'success' => false,
                'message' => 'Template notifikasi tidak ditemukan'
            ];
        }

        // Prepare data for template parsing
        $data = [
            'name' => $document->name,
            'file_name' => $document->file_name,
            'status' => $document->status,
            'uploaded_at' => $document->uploaded_at->format('d-m-Y H:i:s'),
        ];

        // Parse template
        $message = $this->parseTemplate($template['template'], $data);

        // Format phone number (ensure it starts with 62)
        $phone = $this->formatPhoneNumber($document->whatsapp_number);

        // Send WhatsApp notification using form's API key
        $this->dripsenderService->setApiKey($settings->dripsender_api_key);
        return $this->dripsenderService->sendMessage($phone, $message);
    }

    /**
     * Format phone number to ensure it starts with 62
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove any whitespace, dashes, plus signs
        $phone = preg_replace('/\s+|-|\+/', '', $phone);

        // If number starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // If number doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Parse template with variables
     */
    private function parseTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace("{{" . $key . "}}", $value, $template);
        }
        
        return $template;
    }
} 
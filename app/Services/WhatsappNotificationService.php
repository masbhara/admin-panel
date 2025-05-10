<?php

namespace App\Services;

use App\Models\Document;
use App\Models\WhatsappNotification;
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
     * Send notification for document upload
     *
     * @param Document $document
     * @return array
     */
    public function sendDocumentUploadNotification(Document $document): array
    {
        // Check if WhatsApp notification is enabled
        $enabled = Setting::where('key', 'whatsapp_notification_enabled')->value('value');
        if (!$enabled || $enabled === 'false' || $enabled === '0') {
            return [
                'success' => false,
                'message' => 'Notifikasi WhatsApp tidak diaktifkan'
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
        $template = WhatsappNotification::where('event_type', 'document_uploaded')
            ->where('is_active', true)
            ->first();

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
        $message = $template->parseTemplate($data);

        // Format phone number (ensure it starts with 62)
        $phone = $this->formatPhoneNumber($document->whatsapp_number);

        // Send WhatsApp notification
        $result = $this->dripsenderService->sendMessage($phone, $message);

        if ($result['success']) {
            // Update document notification status
            $document->notification_sent = true;
            $document->save();
        }

        return $result;
    }

    /**
     * Format phone number to ensure it starts with 62
     *
     * @param string $phone
     * @return string
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
     * Send notification for document approval
     *
     * @param Document $document
     * @return array
     */
    public function sendDocumentApprovalNotification(Document $document): array
    {
        // Check if WhatsApp notification is enabled
        $enabled = Setting::where('key', 'whatsapp_notification_enabled')->value('value');
        if (!$enabled || $enabled === 'false' || $enabled === '0') {
            return [
                'success' => false,
                'message' => 'Notifikasi WhatsApp tidak diaktifkan'
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
        $template = WhatsappNotification::where('event_type', 'document_approved')
            ->where('is_active', true)
            ->first();

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
        $message = $template->parseTemplate($data);

        // Format phone number (ensure it starts with 62)
        $phone = $this->formatPhoneNumber($document->whatsapp_number);

        // Send WhatsApp notification
        return $this->dripsenderService->sendMessage($phone, $message);
    }

    /**
     * Send notification for document rejection
     *
     * @param Document $document
     * @return array
     */
    public function sendDocumentRejectionNotification(Document $document): array
    {
        // Check if WhatsApp notification is enabled
        $enabled = Setting::where('key', 'whatsapp_notification_enabled')->value('value');
        if (!$enabled || $enabled === 'false' || $enabled === '0') {
            return [
                'success' => false,
                'message' => 'Notifikasi WhatsApp tidak diaktifkan'
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
        $template = WhatsappNotification::where('event_type', 'document_rejected')
            ->where('is_active', true)
            ->first();

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
        $message = $template->parseTemplate($data);

        // Format phone number (ensure it starts with 62)
        $phone = $this->formatPhoneNumber($document->whatsapp_number);

        // Send WhatsApp notification
        return $this->dripsenderService->sendMessage($phone, $message);
    }
} 
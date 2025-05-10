<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class DripsenderService
{
    protected $apiKey;
    protected $webhookUrl;
    protected $apiUrl = 'https://api.dripsender.id';

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Load configuration from settings
     */
    private function loadConfig()
    {
        $this->apiKey = Setting::where('key', 'dripsender_api_key')->value('value');
        $this->webhookUrl = Setting::where('key', 'dripsender_webhook_url')->value('value');
    }

    /**
     * Send WhatsApp message
     *
     * @param string $phone
     * @param string $message
     * @param string|null $mediaUrl
     * @return array
     */
    public function sendMessage(string $phone, string $message, ?string $mediaUrl = null): array
    {
        try {
            $payload = [
                'api_key' => $this->apiKey,
                'phone' => $phone,
                'text' => $message,
            ];

            if ($mediaUrl) {
                $payload['media_url'] = $mediaUrl;
            }

            $response = Http::post("{$this->apiUrl}/send", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Pesan WhatsApp berhasil dikirim',
                    'data' => $response->json()
                ];
            }

            Log::error('Gagal mengirim pesan WhatsApp', [
                'response' => $response->body(),
                'status' => $response->status(),
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan WhatsApp: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Error saat mengirim pesan WhatsApp', [
                'exception' => $e->getMessage(),
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => 'Error saat mengirim pesan WhatsApp: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get all lists from Dripsender
     *
     * @return array
     */
    public function getLists(): array
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey
            ])->get("{$this->apiUrl}/lists");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('Gagal mendapatkan daftar list', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mendapatkan daftar list: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Error saat mendapatkan daftar list', [
                'exception' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error saat mendapatkan daftar list: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get contacts from a specific list
     *
     * @param string $listId
     * @return array
     */
    public function getContacts(string $listId): array
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey
            ])->get("{$this->apiUrl}/lists/{$listId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('Gagal mendapatkan daftar kontak', [
                'response' => $response->body(),
                'status' => $response->status(),
                'list_id' => $listId
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mendapatkan daftar kontak: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Error saat mendapatkan daftar kontak', [
                'exception' => $e->getMessage(),
                'list_id' => $listId
            ]);

            return [
                'success' => false,
                'message' => 'Error saat mendapatkan daftar kontak: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test connection to Dripsender API
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey
            ])->get("{$this->apiUrl}/lists");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Dripsender berhasil'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Dripsender: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saat menghubungi Dripsender: ' . $e->getMessage(),
            ];
        }
    }
} 
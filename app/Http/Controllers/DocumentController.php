<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentSubmitted;
use App\Services\WhatsappNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    protected $whatsappNotificationService;

    public function __construct(WhatsappNotificationService $whatsappNotificationService)
    {
        $this->whatsappNotificationService = $whatsappNotificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debugging lengkap untuk melihat request
        Log::info('==================== DEBUG DOCUMENT SUBMIT ====================');
        Log::info('Dokumen form submission menerima request', [
            'form_id' => $request->document_form_id,
            'name' => $request->name,
            'all_data' => $request->all(),
            'files' => $request->allFiles() ? 'Ada' : 'Tidak Ada',
            'has_file' => $request->hasFile('file'),
            'content_type' => $request->header('Content-Type'),
            'url' => $request->url(),
            'method' => $request->method(),
            'ajax' => $request->ajax(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Log detail file jika ada
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Log::info('Detail file upload:', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'error' => $file->getError(),
                'valid' => $file->isValid(),
            ]);
        } else {
            Log::warning('File tidak ditemukan di request');
        }

        // Cek environtment
        $isDevelopment = app()->environment(['local', 'development', 'testing']);
        
        try {
            // ===== TAHAP 1: Validasi data dasar =====
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:255',
                'document_form_id' => 'required|exists:document_forms,id'
            ], [
                'name.required' => 'Nama lengkap wajib diisi',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
                'whatsapp.max' => 'Nomor WhatsApp tidak boleh lebih dari 20 digit',
                'document_form_id.required' => 'Form ID tidak valid',
                'document_form_id.exists' => 'Form dokumen tidak ditemukan'
            ]);
            
            // Log id yang diterima untuk debugging
            Log::info('Data dasar berhasil divalidasi', $validated);
            Log::info('Document form ID yang diterima: ' . $request->document_form_id . ' (tipe: ' . gettype($request->document_form_id) . ')');
            
            // Pastikan document_form_id adalah integer
            $document_form_id = (int) $request->document_form_id;
            Log::info('Document form ID setelah konversi: ' . $document_form_id);
            $request->merge(['document_form_id' => $document_form_id]);
            
            // ===== TAHAP 2: Validasi dan proses file =====
            // Cek keberadaan file
            if (!$request->hasFile('file')) {
                Log::error('File tidak ditemukan dalam request');
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'File dokumen wajib diunggah']);
            }

            $file = $request->file('file');
            
            // Cek validitas file
            if (!$file->isValid()) {
                $errorMessage = $this->getFileErrorMessage($file->getError());
                Log::error('File tidak valid', ['error' => $errorMessage]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => $errorMessage]);
            }

            // Validasi ukuran dan tipe file
            $maxSize = 10240; // 10MB

            if ($file->getSize() > $maxSize * 1024) {
                Log::error('Ukuran file terlalu besar', ['size' => $file->getSize()]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'Ukuran file tidak boleh lebih dari 10MB']);
            }

            // ===== TAHAP 3: Validasi captcha (hanya di production) =====
            if (!$isDevelopment) {
                $request->validate([
                    'captcha' => 'required|captcha_api:' . $request->captcha_key . ',default'
                ], [
                    'captcha.required' => 'Kode captcha wajib diisi',
                    'captcha.captcha_api' => 'Kode captcha tidak valid'
                ]);
            } else {
                Log::info('Captcha validation bypassed in development environment');
            }

            // ===== TAHAP 4: Proses Form & File =====
            // Get document form
            $documentForm = \App\Models\DocumentForm::find($request->document_form_id);
            
            if (!$documentForm) {
                Log::error('Document form tidak ditemukan dengan ID ' . $request->document_form_id);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['document_form_id' => 'Form dokumen tidak ditemukan']);
            }
            
            Log::info('Document form ditemukan', ['id' => $documentForm->id, 'title' => $documentForm->title]);
            
            // Generate unique filename
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'public/documents/' . $fileName;
            
            // Ensure directory exists
            if (!Storage::exists('public/documents')) {
                Storage::makeDirectory('public/documents');
                Log::info('Created documents directory');
            }
            
            // Upload file using simple put method
            try {
                $fileContent = file_get_contents($file->getRealPath());
                Storage::put($filePath, $fileContent);
                Log::info('File berhasil diupload ke path: ' . $filePath);
            } catch (\Exception $e) {
                // Jika metode di atas gagal, coba metode alternatif dengan storeAs
                Log::warning('Gagal menyimpan file dengan metode put, mencoba metode alternatif', ['error' => $e->getMessage()]);
                try {
                    $filePath = $file->storeAs('public/documents', $fileName);
                    Log::info('File berhasil diupload dengan metode alternatif ke path: ' . $filePath);
                } catch (\Exception $e2) {
                    Log::error('Semua metode upload gagal', ['error' => $e2->getMessage()]);
                    throw new \Exception('Gagal menyimpan file: ' . $e2->getMessage());
                }
            }
            
            // Verify file was uploaded correctly
            if (!Storage::exists($filePath)) {
                Log::error('File gagal disimpan ke storage');
                throw new \Exception('Gagal menyimpan file. Silakan coba lagi.');
            }

            // ===== TAHAP 5: Simpan dokumen ke database =====
            $userId = auth()->check() ? auth()->id() : 1; // Default to admin user if not authenticated
            
            // Debug log untuk memeriksa data sebelum menyimpan ke database
            Log::info('Data yang akan disimpan ke tabel Documents:', [
                'name' => $request->name,
                'document_form_id' => $request->document_form_id,
                'file_path' => $filePath,
                'user_id' => $userId
            ]);
            
            $document = DB::transaction(function () use ($request, $userId, $filePath, $file, $documentForm) {
                try {
                    $document = new Document();
                    $document->name = $request->name;
                    $document->description = 'Dari pengunjung: ' . ($request->name ?: 'Tidak disebutkan');
                    $document->file_path = $filePath;
                    $document->file_name = $file->getClientOriginalName();
                    $document->file_size = $file->getSize();
                    $document->file_type = $file->getMimeType();
                    $document->user_id = $userId;
                    $document->document_form_id = $request->document_form_id;
                    $document->whatsapp_number = $request->whatsapp;
                    $document->notification_sent = false;
                    $document->metadata = [
                        'name' => $request->name,
                        'whatsapp' => $request->whatsapp,
                        'city' => $request->city,
                        'form_title' => $documentForm->title,
                        'form_slug' => $documentForm->slug,
                    ];
                    $document->uploaded_at = now();
                    $document->status = 'pending';
                    
                    $saveResult = $document->save();
                    Log::info('Document save result: ' . ($saveResult ? 'success' : 'failed'));
                    
                    if (!$saveResult) {
                        Log::error('Failed to save document to database');
                        throw new \Exception('Gagal menyimpan dokumen ke database');
                    }
                    
                    Log::info('Document berhasil disimpan dengan ID: ' . $document->id);
                    return $document;
                } catch (\Exception $e) {
                    Log::error('Error dalam transaksi DB saat menyimpan dokumen', [
                        'error' => $e->getMessage(), 
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

            // ===== TAHAP 6: Log activity & kirim notifikasi =====
            // Log activity
            activity()
                ->performedOn($document)
                ->log('uploaded by guest');

            // Kirim notifikasi ke admin
            $admins = User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->get();

            $sender = (object)[
                'name' => $request->name ?: 'Pengunjung',
                'id' => null
            ];

            Notification::send($admins, new DocumentSubmitted($document, $sender));

            // Kirim notifikasi WhatsApp jika nomor tersedia
            if (!empty($request->whatsapp)) {
                $this->whatsappNotificationService->sendDocumentUploadNotification($document);
            }

            // Redirect dengan pesan sukses
            Log::info('Document created successfully', ['id' => $document->id, 'form_id' => $document->document_form_id]);
            return redirect()->back()->with('success', 'Dokumen berhasil diunggah! Terima kasih atas pengiriman Anda.');
        } catch (\Exception $e) {
            Log::error('Error saving document', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Get readable message for file upload error codes.
     */
    private function getFileErrorMessage($errorCode) 
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'Ukuran file melebihi batas maksimum yang diizinkan oleh server';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Ukuran file melebihi batas maksimum yang diizinkan oleh form';
            case UPLOAD_ERR_PARTIAL:
                return 'File hanya terupload sebagian. Silakan coba lagi';
            case UPLOAD_ERR_NO_FILE:
                return 'Tidak ada file yang diunggah';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Folder temporary tidak ditemukan di server';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Gagal menyimpan file ke disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload dihentikan oleh ekstensi';
            default:
                return 'Terjadi kesalahan saat mengunggah file (kode: ' . $errorCode . ')';
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

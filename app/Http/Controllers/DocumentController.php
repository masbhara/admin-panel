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
        // Debugging khusus untuk CSRF token dan form submission
        Log::info('==================== DOCUMENT FORM SUBMISSION START ====================');
        Log::info('Request headers:', [
            'origin' => $request->header('origin'),
            'referer' => $request->header('referer'),
            'user-agent' => $request->header('user-agent'),
            'content-type' => $request->header('content-type'),
            'x-csrf-token' => $request->header('x-csrf-token') ? 'Present' : 'Missing',
            'x-xsrf-token' => $request->header('x-xsrf-token') ? 'Present' : 'Missing'
        ]);
        
        Log::info('Session status:', [
            'has_session' => $request->hasSession(),
            'csrf_token_valid' => $request->hasSession() ? ($request->session()->token() === $request->input('_token') ? 'Yes' : 'No') : 'N/A'
        ]);
        
        // Debugging lengkap untuk melihat request
        Log::info('==================== DEBUG DOCUMENT SUBMIT ====================');
        Log::info('Dokumen form submission menerima request', [
            'form_id' => $request->document_form_id,
            'name' => $request->name,
            'all_data' => $request->all(),
            'files' => $request->allFiles() ? 'Ada' : 'Tidak Ada',
            'has_file' => $request->hasFile('file'),
            'has_screenshot' => $request->hasFile('screenshot'),
            'template_type' => $request->template_type,
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

        // Log detail screenshot jika ada (untuk template artikel)
        if ($request->hasFile('screenshot')) {
            $screenshot = $request->file('screenshot');
            Log::info('Detail screenshot upload:', [
                'original_name' => $screenshot->getClientOriginalName(),
                'size' => $screenshot->getSize(),
                'mime' => $screenshot->getMimeType(),
                'extension' => $screenshot->getClientOriginalExtension(),
                'error' => $screenshot->getError(),
                'valid' => $screenshot->isValid(),
            ]);
        }

        // Cek environtment
        $isDevelopment = app()->environment(['local', 'development', 'testing']);
        
        try {
            // ===== TAHAP 1: Validasi data dasar =====
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:255',
                'document_form_id' => 'required|exists:document_forms,id',
                'template_type' => 'required|string|in:default,article,multiple_article'
            ], [
                'name.required' => 'Nama lengkap wajib diisi',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
                'whatsapp.max' => 'Nomor WhatsApp tidak boleh lebih dari 20 digit',
                'document_form_id.required' => 'Form ID tidak valid',
                'document_form_id.exists' => 'Form dokumen tidak ditemukan',
                'template_type.required' => 'Tipe template wajib diisi',
                'template_type.in' => 'Tipe template tidak valid'
            ]);
            
            // Log id yang diterima untuk debugging
            Log::info('Data dasar berhasil divalidasi', $validated);
            Log::info('Document form ID yang diterima: ' . $request->document_form_id . ' (tipe: ' . gettype($request->document_form_id) . ')');
            Log::info('Template type yang diterima: ' . $request->template_type);
            
            // Pastikan document_form_id adalah integer
            $document_form_id = (int) $request->document_form_id;
            Log::info('Document form ID setelah konversi: ' . $document_form_id);
            $request->merge(['document_form_id' => $document_form_id]);
            
            // Get document form
            $documentForm = \App\Models\DocumentForm::find($request->document_form_id);
            
            if (!$documentForm) {
                Log::error('Document form tidak ditemukan dengan ID ' . $request->document_form_id);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['document_form_id' => 'Form dokumen tidak ditemukan']);
            }
            
            Log::info('Document form ditemukan', [
                'id' => $documentForm->id, 
                'title' => $documentForm->title,
                'template_type' => $documentForm->template_type
            ]);
            
            // Verifikasi template type di request sesuai dengan di database
            if (strtolower($request->template_type) !== strtolower($documentForm->template_type)) {
                Log::error('Template type tidak sesuai', [
                    'request_template' => $request->template_type,
                    'db_template' => $documentForm->template_type
                ]);
                
                // Coba tetap gunakan template dari database untuk mencegah error
                Log::info('Mencoba menggunakan template dari database: ' . $documentForm->template_type);
                $request->merge(['template_type' => $documentForm->template_type]);
            }
            
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
            
            // ===== TAHAP 2.1: Validasi tambahan untuk template artikel =====
            $screenshotPath = null;
            $screenshotMediaPath = null; // Tambahkan variabel untuk screenshot media

            if ($request->template_type === 'article' || $request->template_type === 'multiple_article') {
                // Logging detail untuk debugging template artikel
                Log::info('Memproses template ' . $request->template_type . ' dengan data:', [
                    'media_link' => $request->media_link,
                    'has_screenshot' => $request->hasFile('screenshot'),
                    'has_screenshot_media' => $request->hasFile('screenshot_media'),
                ]);
                
                // Validasi media link
                // Untuk multiple_article, tidak perlu validasi URL
                if ($request->template_type === 'article') {
                    $request->validate([
                        'media_link' => 'required|url|max:2000'
                    ], [
                        'media_link.required' => 'Link media wajib diisi',
                        'media_link.url' => 'Format URL tidak valid',
                        'media_link.max' => 'URL terlalu panjang (maksimal 2000 karakter)'
                    ]);
                } else {
                    $request->validate([
                        'media_link' => 'required|max:2000'
                    ], [
                        'media_link.required' => 'Link media wajib diisi',
                        'media_link.max' => 'URL terlalu panjang (maksimal 2000 karakter)'
                    ]);
                }
                
                Log::info('Validasi media_link berhasil');
                
                // Validasi screenshot
                if (!$request->hasFile('screenshot')) {
                    Log::error('Screenshot tidak ditemukan dalam request untuk template ' . $request->template_type);
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['screenshot' => 'Screenshot wajib diunggah']);
                }
                
                $screenshot = $request->file('screenshot');
                
                Log::info('Screenshot ditemukan dalam request', [
                    'file_name' => $screenshot->getClientOriginalName(),
                    'mime_type' => $screenshot->getMimeType()
                ]);
                
                // Cek validitas screenshot
                if (!$screenshot->isValid()) {
                    $errorMessage = $this->getFileErrorMessage($screenshot->getError());
                    Log::error('Screenshot tidak valid', ['error' => $errorMessage]);
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['screenshot' => $errorMessage]);
                }
                
                // Validasi tipe dan ukuran screenshot
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($screenshot->getMimeType(), $allowedTypes)) {
                    Log::error('Tipe screenshot tidak valid', ['mime' => $screenshot->getMimeType()]);
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['screenshot' => 'Tipe file tidak valid. Hanya menerima file JPG atau PNG']);
                }
                
                if ($screenshot->getSize() > 5 * 1024 * 1024) { // 5MB
                    Log::error('Ukuran screenshot terlalu besar', ['size' => $screenshot->getSize()]);
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['screenshot' => 'Ukuran file tidak boleh lebih dari 5MB']);
                }
                
                Log::info('Validasi screenshot berhasil, akan mengupload screenshot');
                
                // Upload screenshot
                $screenshotFileName = 'screenshot_' . pathinfo($screenshot->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $screenshot->getClientOriginalExtension();
                $screenshotPath = 'public/screenshots/' . $screenshotFileName;
                
                // Ensure directory exists
                if (!Storage::exists('public/screenshots')) {
                    Storage::makeDirectory('public/screenshots');
                    Log::info('Created screenshots directory');
                }
                
                // Debug info untuk storage path
                Log::info('Storage path details', [
                    'disk' => config('filesystems.default'),
                    'base_path' => storage_path(),
                    'app_url' => config('app.url'),
                    'screenshot_full_path' => storage_path('app/' . $screenshotPath),
                    'public_url' => Storage::url($screenshotPath),
                    'directory_exists' => Storage::exists('public/screenshots'),
                ]);
                
                // Upload screenshot
                try {
                    $screenshotContent = file_get_contents($screenshot->getRealPath());
                    Storage::put($screenshotPath, $screenshotContent);
                    Log::info('Screenshot berhasil diupload ke path: ' . $screenshotPath);
                } catch (\Exception $e) {
                    Log::error('Gagal menyimpan screenshot', ['error' => $e->getMessage()]);
                    throw new \Exception('Gagal menyimpan screenshot: ' . $e->getMessage());
                }
                
                // Verify screenshot was uploaded correctly
                if (!Storage::exists($screenshotPath)) {
                    Log::error('Screenshot gagal disimpan ke storage');
                    throw new \Exception('Gagal menyimpan screenshot. Silakan coba lagi.');
                }
                
                Log::info('Screenshot berhasil diupload dan diverifikasi');
                
                // Khusus untuk multiple_article: proses screenshot_media
                if ($request->template_type === 'multiple_article') {
                    // Validasi screenshot_media
                    if (!$request->hasFile('screenshot_media')) {
                        Log::error('Screenshot media tidak ditemukan dalam request untuk template multiple_article');
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['screenshot_media' => 'Screenshot kirim ke media wajib diunggah']);
                    }
                    
                    $screenshotMedia = $request->file('screenshot_media');
                    
                    Log::info('Screenshot media ditemukan dalam request', [
                        'file_name' => $screenshotMedia->getClientOriginalName(),
                        'mime_type' => $screenshotMedia->getMimeType()
                    ]);
                    
                    // Cek validitas screenshot media
                    if (!$screenshotMedia->isValid()) {
                        $errorMessage = $this->getFileErrorMessage($screenshotMedia->getError());
                        Log::error('Screenshot media tidak valid', ['error' => $errorMessage]);
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['screenshot_media' => $errorMessage]);
                    }
                    
                    // Validasi tipe dan ukuran screenshot media
                    if (!in_array($screenshotMedia->getMimeType(), $allowedTypes)) {
                        Log::error('Tipe screenshot media tidak valid', ['mime' => $screenshotMedia->getMimeType()]);
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['screenshot_media' => 'Tipe file tidak valid. Hanya menerima file JPG atau PNG']);
                    }
                    
                    if ($screenshotMedia->getSize() > 5 * 1024 * 1024) { // 5MB
                        Log::error('Ukuran screenshot media terlalu besar', ['size' => $screenshotMedia->getSize()]);
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['screenshot_media' => 'Ukuran file tidak boleh lebih dari 5MB']);
                    }
                    
                    Log::info('Validasi screenshot media berhasil, akan mengupload screenshot media');
                    
                    // Upload screenshot media
                    $screenshotMediaFileName = 'screenshot_media_' . pathinfo($screenshotMedia->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $screenshotMedia->getClientOriginalExtension();
                    $screenshotMediaPath = 'public/screenshots/' . $screenshotMediaFileName;
                    
                    // Upload screenshot media
                    try {
                        $screenshotMediaContent = file_get_contents($screenshotMedia->getRealPath());
                        Storage::put($screenshotMediaPath, $screenshotMediaContent);
                        Log::info('Screenshot media berhasil diupload ke path: ' . $screenshotMediaPath);
                    } catch (\Exception $e) {
                        Log::error('Gagal menyimpan screenshot media', ['error' => $e->getMessage()]);
                        throw new \Exception('Gagal menyimpan screenshot media: ' . $e->getMessage());
                    }
                    
                    // Verify screenshot media was uploaded correctly
                    if (!Storage::exists($screenshotMediaPath)) {
                        Log::error('Screenshot media gagal disimpan ke storage');
                        throw new \Exception('Gagal menyimpan screenshot media. Silakan coba lagi.');
                    }
                    
                    Log::info('Screenshot media berhasil diupload dan diverifikasi');
                }
            }

            // ===== TAHAP 3: Validasi captcha (hanya di production) =====
            if (!$isDevelopment && $documentForm->captcha_enabled) {
                $request->validate([
                    'captcha' => 'required|captcha_api:' . $request->captcha_key . ',default'
                ], [
                    'captcha.required' => 'Kode captcha wajib diisi',
                    'captcha.captcha_api' => 'Kode captcha tidak valid'
                ]);
                Log::info('Captcha validation performed');
            } else {
                Log::info('Captcha validation bypassed: ' . 
                    ($isDevelopment ? 'Development environment' : 'Captcha disabled for this form'));
            }

            // ===== TAHAP 4: Proses Form & File =====
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
            
            // Siapkan metadata dasar
            $metadata = [
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'city' => $request->city,
                'form_title' => $documentForm->title,
                'form_slug' => $documentForm->slug,
                'template_type' => $documentForm->template_type
            ];
            
            // Tambahkan metadata khusus untuk template artikel dan multiple_article
            if ($request->template_type === 'article' || $request->template_type === 'multiple_article') {
                Log::info('Menambahkan metadata khusus untuk template ' . $request->template_type);
                $metadata['media_link'] = $request->media_link;
                
                if ($screenshotPath) {
                    $metadata['screenshot_path'] = $screenshotPath;
                    Log::info('Screenshot path ditambahkan ke metadata: ' . $screenshotPath);
                } else {
                    Log::warning('Screenshot path kosong, tidak ditambahkan ke metadata');
                }
                
                // Tambahkan metadata untuk screenshot media jika ada
                if ($request->template_type === 'multiple_article' && $screenshotMediaPath) {
                    $metadata['screenshot_media_path'] = $screenshotMediaPath;
                    Log::info('Screenshot media path ditambahkan ke metadata: ' . $screenshotMediaPath);
                }
            }
            
            // Debug log untuk memeriksa data sebelum menyimpan ke database
            Log::info('Data yang akan disimpan ke tabel Documents:', [
                'name' => $request->name,
                'document_form_id' => $request->document_form_id,
                'file_path' => $filePath,
                'user_id' => $userId,
                'template_type' => $request->template_type,
                'metadata' => $metadata
            ]);
            
            $document = DB::transaction(function () use ($request, $userId, $filePath, $file, $documentForm, $metadata, $screenshotPath, $screenshotMediaPath) {
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
                    
                    // Simpan metadata
                    if ($request->template_type === 'article' || $request->template_type === 'multiple_article') {
                        Log::info('Menyimpan dokumen dengan template ' . $request->template_type, [
                            'media_link' => $request->media_link,
                            'screenshot_path' => $screenshotPath,
                            'screenshot_media_path' => $screenshotMediaPath
                        ]);
                        
                        // Pastikan media_link dan screenshot_path tersimpan dalam metadata
                        $metadata['media_link'] = $request->media_link;
                        $metadata['screenshot_path'] = $screenshotPath;
                        
                        // Tambahkan screenshot_media_path jika ada
                        if ($request->template_type === 'multiple_article' && $screenshotMediaPath) {
                            $metadata['screenshot_media_path'] = $screenshotMediaPath;
                        }
                    }
                    
                    // Simpan metadata ke dokumen
                    $document->metadata = $metadata;
                    Log::info('Metadata yang akan disimpan:', $metadata);
                    
                    $document->uploaded_at = now();
                    $document->status = 'pending';
                    
                    $saveResult = $document->save();
                    Log::info('Document save result: ' . ($saveResult ? 'success' : 'failed'));
                    
                    if (!$saveResult) {
                        Log::error('Failed to save document to database');
                        throw new \Exception('Gagal menyimpan dokumen ke database');
                    }
                    
                    Log::info('Document berhasil disimpan dengan ID: ' . $document->id);
                    
                    // Verifikasi data yang tersimpan
                    $savedDocument = Document::find($document->id);
                    Log::info('Data dokumen yang tersimpan:', [
                        'id' => $savedDocument->id,
                        'metadata' => $savedDocument->metadata,
                        'has_media_link' => isset($savedDocument->metadata['media_link']),
                        'has_screenshot' => isset($savedDocument->metadata['screenshot_path']),
                        'has_screenshot_media' => isset($savedDocument->metadata['screenshot_media_path'])
                    ]);
                    
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

            Log::info('Activity log dibuat untuk dokumen ID: ' . $document->id);

            // Kirim notifikasi ke admin
            try {
                $admins = User::whereHas('roles', function($query) {
                    $query->where('name', 'admin');
                })->get();

                $sender = (object)[
                    'name' => $request->name ?: 'Pengunjung',
                    'id' => null
                ];

                Notification::send($admins, new DocumentSubmitted($document, $sender));
                Log::info('Notifikasi berhasil dikirim ke admin');
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi ke admin', ['error' => $e->getMessage()]);
                // Jangan berhenti proses jika notifikasi gagal
            }

            // Kirim notifikasi WhatsApp jika nomor tersedia
            if (!empty($request->whatsapp)) {
                try {
                    $this->whatsappNotificationService->sendDocumentUploadNotification($document);
                    Log::info('Notifikasi WhatsApp berhasil dikirim ke: ' . $request->whatsapp);
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim notifikasi WhatsApp', ['error' => $e->getMessage()]);
                    // Jangan berhenti proses jika notifikasi gagal
                }
            }

            // Tentukan pesan sukses berdasarkan tipe template
            $successMessage = 'Dokumen berhasil diunggah! Terima kasih atas pengiriman Anda.';
            if ($request->template_type === 'article') {
                $successMessage = 'Artikel dan screenshot media berhasil diunggah! Terima kasih atas kontribusi Anda.';
            } else if ($request->template_type === 'multiple_article') {
                $successMessage = 'Artikel dan kedua screenshot berhasil diunggah! Terima kasih atas kontribusi Anda.';
            }

            // Redirect dengan pesan sukses
            Log::info('Document created successfully', [
                'id' => $document->id, 
                'form_id' => $document->document_form_id,
                'template_type' => $request->template_type
            ]);
            
            return redirect()->back()->with('success', $successMessage);
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

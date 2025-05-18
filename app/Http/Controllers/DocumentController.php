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
        try {
            // Log data request untuk debugging
            Log::info('Document upload attempt with data:', [
                'document_form_id' => $request->document_form_id,
                'name' => $request->name,
                'has_file' => $request->hasFile('file'),
                'request_data' => $request->all(),
            ]);

            // Tambahkan validasi khusus: document_form_id wajib ada
            $request->validate([
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:255',
                'file' => 'required|file|max:10240',
                'captcha' => 'required|captcha_api:' . $request->captcha_key . ',default',
                'document_form_id' => 'required|exists:document_forms,id', // Wajib ada
            ], [
                'captcha.captcha_api' => 'Kode captcha tidak valid. Silakan coba lagi.',
                'name.required' => 'Nama lengkap wajib diisi.',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
                'whatsapp.max' => 'Nomor WhatsApp tidak boleh lebih dari 20 digit.',
                'file.required' => 'File dokumen wajib diunggah.',
                'file.file' => 'Tipe file tidak valid. Silakan unggah file dokumen.',
                'file.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
                'document_form_id.required' => 'Form ID tidak valid. Silakan reload halaman.',
                'document_form_id.exists' => 'Form dokumen tidak valid.',
            ]);

            if (!$request->hasFile('file')) {
                Log::error('File missing in request');
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'File dokumen wajib diupload']);
            }

            $file = $request->file('file');
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Menyimpan file di public/documents untuk konsistensi
            $filePath = $file->storeAs('public/documents', $fileName);
            
            // Log penyimpanan file untuk debugging
            Log::info('Public dokumen stored: ' . $fileName . ' at path: ' . $filePath);

            // Pastikan document_form_id ada dan valid
            $documentFormId = $request->input('document_form_id');
            Log::info('Using document_form_id for document: ' . $documentFormId);

            // Ambil document form untuk keperluan logging dan validasi
            $documentForm = \App\Models\DocumentForm::find($documentFormId);
            if (!$documentForm) {
                Log::error('Form dokumen dengan ID ' . $documentFormId . ' tidak ditemukan');
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Form dokumen tidak valid. Silakan coba lagi.']);
            }
            
            Log::info('Found document form: ' . $documentForm->title . ' (ID: ' . $documentForm->id . ')');

            // Tentukan user_id (menggunakan user id=1 jika tidak terautentikasi)
            $userId = auth()->check() ? auth()->id() : 1;

            // Simpan dokumen ke database dengan transaction untuk memastikan konsistensi
            DB::beginTransaction();
            try {
                $document = Document::create([
                    'name' => $request->name,
                    'description' => 'Dari pengunjung: ' . ($request->name ?: 'Tidak disebutkan'),
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'user_id' => $userId,
                    'document_form_id' => $documentFormId,
                    'whatsapp_number' => $request->whatsapp,
                    'notification_sent' => false,
                    'metadata' => [
                        'name' => $request->name,
                        'whatsapp' => $request->whatsapp,
                        'city' => $request->city,
                        'form_title' => $documentForm->title,
                        'form_slug' => $documentForm->slug,
                    ],
                    'uploaded_at' => now(),
                    'status' => 'pending',
                ]);

                Log::info('Document created successfully:', [
                    'id' => $document->id,
                    'document_form_id' => $document->document_form_id,
                    'form_title' => $documentForm->title,
                    'form_slug' => $documentForm->slug
                ]);
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Failed to save document: ' . $e->getMessage());
                throw $e;
            }

            // Log aktivitas
            activity()
                ->performedOn($document)
                ->log('uploaded by guest');

            // Kirim notifikasi ke semua admin
            $admins = User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->get();

            // Buat objek sender untuk notifikasi
            $sender = (object)[
                'name' => $request->name ?: 'Pengunjung',
                'id' => null
            ];

            // Kirim notifikasi ke semua admin dengan tipe yang benar
            Notification::send($admins, new DocumentSubmitted($document, $sender));

            // Kirim notifikasi WhatsApp jika nomor tersedia
            if (!empty($request->whatsapp)) {
                $notificationResult = $this->whatsappNotificationService->sendDocumentUploadNotification($document);
                
                if (!$notificationResult['success']) {
                    Log::warning('Gagal mengirim notifikasi WhatsApp: ' . $notificationResult['message'], [
                        'document_id' => $document->id,
                        'phone' => $request->whatsapp
                    ]);
                } else {
                    Log::info('Notifikasi WhatsApp berhasil dikirim', [
                        'document_id' => $document->id,
                        'phone' => $request->whatsapp
                    ]);
                }
            }

            // Redirect dengan sukses dan flash message jelas
            return redirect()->back()->with('success', 'Dokumen berhasil diunggah! Terima kasih atas pengiriman Anda.');
        } catch (\Exception $e) {
            Log::error('Error uploading public document: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengunggah dokumen: ' . $e->getMessage()]);
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

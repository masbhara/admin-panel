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
            $request->validate([
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:255',
                'file' => 'required|file|max:10240',
                'captcha' => 'required|captcha_api:' . $request->captcha_key . ',default',
            ], [
                'captcha.captcha_api' => 'Kode captcha tidak valid. Silakan coba lagi.',
                'name.required' => 'Nama lengkap wajib diisi.',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
                'whatsapp.max' => 'Nomor WhatsApp tidak boleh lebih dari 20 digit.',
                'file.required' => 'File dokumen wajib diunggah.',
                'file.file' => 'Tipe file tidak valid. Silakan unggah file dokumen.',
                'file.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
            ]);

            if (!$request->hasFile('file')) {
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

            // Simpan dokumen ke database
            $document = Document::create([
                'name' => $request->name,
                'description' => 'Dari pengunjung: ' . ($request->name ?: 'Tidak disebutkan'),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
                'user_id' => auth()->check() ? auth()->id() : null,
                'whatsapp_number' => $request->whatsapp,
                'notification_sent' => false,
                'metadata' => [
                    'whatsapp' => $request->whatsapp,
                    'city' => $request->city,
                ],
                'uploaded_at' => now(),
                'status' => 'pending',
            ]);

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

            // Redirect dengan sukses
            return redirect()->back()->with('success', 'Dokumen berhasil diunggah! Terima kasih.');
        } catch (\Exception $e) {
            Log::error('Error uploading public document: ' . $e->getMessage());
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

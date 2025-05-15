<?php

namespace App\Http\Controllers\Admin\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Document;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsappNotificationService;
use Illuminate\Support\Facades\DB;

class CrudController extends Controller
{
    protected $whatsappNotificationService;

    public function __construct(WhatsappNotificationService $whatsappNotificationService)
    {
        $this->whatsappNotificationService = $whatsappNotificationService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        $documents = Document::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    // Pencarian di kolom utama
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('file_name', 'like', "%{$search}%")
                      ->orWhere('file_type', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");

                    // Pencarian di metadata (JSON)
                    $q->orWhereRaw("LOWER(JSON_EXTRACT(metadata, '$.pengirim')) LIKE ?", ['%' . strtolower($search) . '%'])
                      ->orWhereRaw("LOWER(JSON_EXTRACT(metadata, '$.whatsapp')) LIKE ?", ['%' . strtolower($search) . '%'])
                      ->orWhereRaw("LOWER(JSON_EXTRACT(metadata, '$.city')) LIKE ?", ['%' . strtolower($search) . '%']);

                    // Pencarian berdasarkan user
                    $q->orWhereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->with('user')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        // Hitung statistik dokumen
        $stats = [
            'total' => Document::count(),
            'approved' => Document::where('status', 'approved')->count(),
            'pending' => Document::where('status', 'pending')->count(),
            'rejected' => Document::where('status', 'rejected')->count(),
        ];

        // Log statistik untuk debugging
        \Log::info('Document Stats:', [
            'stats' => $stats,
            'total_documents' => $documents->total(),
            'current_page' => $documents->currentPage(),
            'per_page' => $perPage
        ]);

        // Tambahkan stats ke collection dokumen
        $documents->stats = $stats;

        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
            'filters' => $request->only(['search', 'per_page']),
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Documents/Create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'required|file|max:10240', // Max 10MB
                'metadata' => 'nullable|array',
            ]);

            if (!$request->hasFile('file')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'File dokumen wajib diupload']);
            }

            $file = $request->file('file');
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Pastikan file disimpan di public/documents
            $filePath = $file->storeAs('public/documents', $fileName);
            
            // Log untuk debugging
            Log::info('Storing new document: ' . $fileName . ' at path: ' . $filePath);

            $document = Document::create([
                'name' => $request->name,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
                'user_id' => auth()->id(),
                'uploaded_at' => Carbon::now(),
                'status' => 'pending',
                'metadata' => $request->metadata,
            ]);

            // Log activity
            activity()
                ->performedOn($document)
                ->causedBy(auth()->user())
                ->withProperties(['name' => $document->name])
                ->log('created document');

            return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error storing document: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show(Document $document)
    {
        $activities = Activity::where('subject_type', Document::class)
            ->where('subject_id', $document->id)
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Admin/Documents/Show', [
            'document' => $document->load('user'),
            'activities' => $activities,
        ]);
    }

    public function edit(Document $document)
    {
        return Inertia::render('Admin/Documents/Edit', [
            'document' => $document,
        ]);
    }

    public function update(Request $request, Document $document)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'nullable|file|max:10240', // Max 10MB
                'status' => 'required|in:pending,approved,rejected',
                'metadata' => 'nullable|array',
            ]);

            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'metadata' => $request->metadata,
            ];

            // Jika ada file baru yang diunggah
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Hapus file lama jika ada
                if ($document->file_path && Storage::exists($document->file_path)) {
                    Storage::delete($document->file_path);
                }
                
                $filePath = $file->storeAs('public/documents', $fileName);

                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_size'] = $file->getSize();
                $data['file_type'] = $file->getClientMimeType();
            }

            $document->update($data);

            // Log activity
            activity()
                ->performedOn($document)
                ->causedBy(auth()->user())
                ->withProperties([
                    'name' => $document->name,
                    'status' => $document->status,
                    'file_updated' => $request->hasFile('file')
                ])
                ->log('updated document');

            return redirect()->route('admin.documents.show', $document)->with('success', 'Dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Document $document)
    {
        try {
            // Hapus file dari storage
            if ($document->file_path && Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }

            // Hapus file preview jika ada
            $previewPath = 'public/previews/' . pathinfo($document->file_path, PATHINFO_FILENAME) . '.pdf';
            if (Storage::exists($previewPath)) {
                Storage::delete($previewPath);
            }

            // Simpan nama untuk log
            $documentName = $document->name;
            
            // Hapus dokumen
            $document->delete();

            // Log aktivitas
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['name' => $documentName])
                ->log('deleted document');

            return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Update status dokumen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Document $document)
    {
        try {
            // Validasi request
            $validatedData = $request->validate([
                'status' => 'required|in:pending,approved,rejected',
            ]);

            // Simpan status lama untuk log
            $oldStatus = $document->status;
            
            // Update status dokumen
            $document->update([
                'status' => $request->status,
            ]);

            // Siapkan pesan berdasarkan status
            $statusMessages = [
                'pending' => 'menunggu',
                'approved' => 'disetujui',
                'rejected' => 'ditolak',
            ];

            // Log aktivitas
            activity()
                ->performedOn($document)
                ->causedBy(auth()->user())
                ->withProperties([
                    'name' => $document->name,
                    'old_status' => $oldStatus,
                    'new_status' => $document->status,
                ])
                ->log('changed document status');

            // Kirim notifikasi WhatsApp jika dokumen memiliki nomor WhatsApp dan status berubah
            $notificationSent = false;
            if (!empty($document->whatsapp_number) && $oldStatus !== $request->status) {
                // Siapkan event_type berdasarkan status baru
                $eventType = '';
                if ($request->status === 'approved') {
                    $eventType = 'document_approved';
                } elseif ($request->status === 'rejected') {
                    $eventType = 'document_rejected';
                }

                // Hanya kirim notifikasi untuk status approved atau rejected
                if ($eventType) {
                    $result = $this->sendWhatsAppNotification($document, $eventType);
                    $notificationSent = $result['success'];
                    
                    // Log hasil notifikasi
                    if ($notificationSent) {
                        Log::info("WhatsApp notification sent for document {$document->id}", [
                            'document_id' => $document->id,
                            'phone' => $document->whatsapp_number,
                            'status' => $request->status,
                        ]);
                    } else {
                        Log::warning("Failed to send WhatsApp notification for document {$document->id}", [
                            'document_id' => $document->id,
                            'phone' => $document->whatsapp_number,
                            'status' => $request->status,
                            'error' => $result['message'],
                        ]);
                    }
                }
            }

            $successMessage = "Status dokumen berhasil diubah menjadi {$statusMessages[$request->status]}.";
            if ($notificationSent) {
                $successMessage .= " Notifikasi WhatsApp telah dikirim ke nomor terkait.";
            }

            // Untuk semua jenis request, selalu gunakan redirect ke halaman yang sama
            // dengan flash message untuk menghindari response JSON mentah
            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error updating document status: ' . $e->getMessage());
            
            // Selalu gunakan redirect untuk semua jenis request
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Send WhatsApp notification based on event type
     * 
     * @param Document $document
     * @param string $eventType
     * @return array
     */
    private function sendWhatsAppNotification(Document $document, string $eventType): array
    {
        try {
            $notificationMethod = '';
            
            switch ($eventType) {
                case 'document_approved':
                    return $this->whatsappNotificationService->sendDocumentApprovalNotification($document);
                case 'document_rejected':
                    return $this->whatsappNotificationService->sendDocumentRejectionNotification($document);
                default:
                    return [
                        'success' => false,
                        'message' => 'Tipe notifikasi tidak didukung'
                    ];
            }
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp notification: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'event_type' => $eventType
            ]);
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk delete documents
     */
    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'message' => 'Tidak ada dokumen yang dipilih'
                ], 400);
            }

            // Mulai transaksi
            DB::beginTransaction();
            
            try {
                // Ambil dokumen yang akan dihapus
                $documents = Document::whereIn('id', $ids)->get();
                $deletedCount = 0;
                
                foreach ($documents as $document) {
                    // Hapus file fisik jika ada
                    if ($document->file_path && Storage::exists($document->file_path)) {
                        Storage::delete($document->file_path);
                    }
                    
                    // Hapus file preview jika ada
                    $previewPath = 'public/previews/' . pathinfo($document->file_path, PATHINFO_FILENAME) . '.pdf';
                    if (Storage::exists($previewPath)) {
                        Storage::delete($previewPath);
                    }
                    
                    // Log aktivitas
                    activity()
                        ->causedBy(auth()->user())
                        ->performedOn($document)
                        ->log('deleted document in bulk operation');
                        
                    // Hapus dokumen
                    $document->delete();
                    $deletedCount++;
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => $deletedCount . ' dokumen berhasil dihapus'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error bulk deleting documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
} 
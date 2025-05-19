<?php

namespace App\Http\Controllers\Admin\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Services\WhatsappNotificationService;

class CrudController extends Controller
{
    protected $whatsappNotificationService;

    public function __construct(WhatsappNotificationService $whatsappNotificationService)
    {
        $this->whatsappNotificationService = $whatsappNotificationService;
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        // Pastikan dokumen terisi dengan relasi yang dibutuhkan
        $document->load(['documentForm', 'user']);
        
        return Inertia::render('Admin/Documents/Show', [
            'document' => $document,
        ]);
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document)
    {
        // Pastikan dokumen terisi dengan relasi yang dibutuhkan
        $document->load('documentForm');
        
        return Inertia::render('Admin/Documents/Edit', [
            'document' => $document,
        ]);
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'file' => 'nullable|file|max:10240', // 10MB
            'notes' => 'nullable|string',
        ]);

        try {
            // Update metadata
            $metadata = $document->metadata ?? [];
            $metadata['name'] = $validated['name'];
            $metadata['whatsapp'] = $validated['whatsapp'] ?? null;
            $metadata['city'] = $validated['city'] ?? null;
            
            // Jika ada file baru
            if ($request->hasFile('file')) {
                // Simpan file baru
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . $document->id . '.' . $extension;
                $filePath = 'public/documents/' . $fileName;
                
                // Simpan file
                Storage::put($filePath, file_get_contents($file->getRealPath()));
                
                // Verifikasi file berhasil disimpan
                if (!Storage::exists($filePath)) {
                    throw new \Exception('Gagal mengupload file');
                }
                
                // Hapus file lama jika ada
                if ($document->file_path && Storage::exists($document->file_path)) {
                    Storage::delete($document->file_path);
                }
                
                // Update file path dan name
                $document->file_path = $filePath;
                $document->file_name = $file->getClientOriginalName();
                $document->file_size = $file->getSize();
                $document->file_type = $file->getMimeType();
            }
            
            // Update dokumen
            $document->name = $validated['name'];
            $document->metadata = $metadata;
            $document->notes = $validated['notes'] ?? null;
            
            // Update status jika ada
            if (isset($validated['status'])) {
                $document->status = $validated['status'];
            }
            
            $document->save();
            
            return redirect()->back()->with('success', 'Dokumen berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui dokumen', [
                'error' => $e->getMessage(),
                'document_id' => $document->id
            ]);
            
            return redirect()->back()->withErrors([
                'error' => 'Gagal memperbarui dokumen: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update status of the specified document.
     */
    public function updateStatus(Request $request, Document $document)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        try {
            // Update status dokumen
            $document->status = $validated['status'];
            $document->save();
            
            // Kirim notifikasi WhatsApp berdasarkan status
            if ($validated['status'] === 'approved') {
                $this->whatsappNotificationService->sendDocumentApprovalNotification($document);
            } elseif ($validated['status'] === 'rejected') {
                $this->whatsappNotificationService->sendDocumentRejectionNotification($document);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status dokumen berhasil diperbarui',
                'document' => $document
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui status dokumen', [
                'error' => $e->getMessage(),
                'document_id' => $document->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified document.
     */
    public function destroy(Document $document)
    {
        try {
            // Hapus file jika ada
            if ($document->file_path && Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
            
            // Simpan ID dokumen form untuk redirect
            $documentFormId = $document->document_form_id;
            
            // Hapus dokumen
            $document->delete();
            
            // Jika request dari Ajax, kembalikan response JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil dihapus'
                ]);
            }
            
            // Redirect ke halaman dokumen form
            return redirect()->route('admin.document-forms.show', $documentFormId)
                ->with('success', 'Dokumen berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error saat menghapus dokumen', [
                'error' => $e->getMessage(),
                'document_id' => $document->id
            ]);
            
            // Jika request dari Ajax, kembalikan response JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors([
                'error' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ]);
        }
    }
} 
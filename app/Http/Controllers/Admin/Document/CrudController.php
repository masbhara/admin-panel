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

class CrudController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        $documents = Document::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->with('user') // Assuming documents are linked to users
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
            'filters' => $request->only(['search', 'per_page']),
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

            // Untuk semua jenis request, selalu gunakan redirect ke halaman yang sama
            // dengan flash message untuk menghindari response JSON mentah
            return redirect()->back()->with('success', "Status dokumen berhasil diubah menjadi {$statusMessages[$request->status]}.");
        } catch (\Exception $e) {
            Log::error('Error updating document status: ' . $e->getMessage());
            
            // Selalu gunakan redirect untuk semua jenis request
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
} 
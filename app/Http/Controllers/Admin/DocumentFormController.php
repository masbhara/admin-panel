<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentForm;
use App\Models\Document;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DocumentFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $documentForms = DocumentForm::query()
            ->when(!$user->hasRole('super-admin'), function($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/DocumentForms/Index', [
            'documentForms' => $documentForms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/DocumentForms/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slug' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('document_forms', 'slug'),
            ],
            'submission_deadline' => 'nullable|date',
            'closed_message' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'template_type' => 'required|string|in:default,article'
        ], [
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Slug sudah digunakan, silakan gunakan slug yang lain.',
            'template_type.required' => 'Tipe template harus dipilih.',
            'template_type.in' => 'Tipe template tidak valid.'
        ]);

        $user = Auth::user();
        
        try {
            DB::beginTransaction();

            // Parse dan format submission_deadline jika ada
            if (isset($validated['submission_deadline'])) {
                $validated['submission_deadline'] = Carbon::parse($validated['submission_deadline']);
            }

            // Buat slug dari judul jika tidak ada
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['title']);
                
                // Pastikan slug unik
                $count = 0;
                $originalSlug = $validated['slug'];
                while (DocumentForm::where('slug', $validated['slug'])->exists()) {
                    $count++;
                    $validated['slug'] = $originalSlug . '-' . $count;
                }
            }
            
            // Tambahkan user_id
            $validated['user_id'] = $user->id;
            
            // Buat document form
            $documentForm = DocumentForm::create($validated);
            
            // Dapatkan fields default berdasarkan template yang dipilih
            $fields = DocumentForm::getDefaultFields($validated['template_type']);
            
            // Buat form fields
            foreach ($fields as $field) {
                $documentForm->formFields()->create($field);
            }
            
            DB::commit();
            
            return redirect()->route('admin.document-forms.index')
                ->with('success', 'Form dokumen berhasil dibuat.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating document form: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, DocumentForm $documentForm)
    {
        $this->authorize('view', $documentForm);

        $documentForm->load('user:id,name,email');
        
        // Ambil per_page dari request atau gunakan default 10
        $perPage = $request->input('per_page', 10);
        
        // Debug: Log query dan hasil untuk melihat masalah
        $documentCount = Document::where('document_form_id', $documentForm->id)->count();
        Log::info('DocumentForm show details', [
            'document_form_id' => $documentForm->id,
            'document_count' => $documentCount,
            'documents_exist' => $documentCount > 0,
            'per_page' => $perPage
        ]);
        
        // Query documents dengan filtering
        $query = Document::where('document_form_id', $documentForm->id);
        
        // Hitung jumlah dokumen per status untuk keseluruhan data dengan satu kueri
        $statusCounts = Document::where('document_form_id', $documentForm->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Pastikan semua status ada dalam array, meskipun countnya 0
        $statusCounts = [
            'pending' => $statusCounts['pending'] ?? 0,
            'approved' => $statusCounts['approved'] ?? 0,
            'rejected' => $statusCounts['rejected'] ?? 0,
        ];
        
        // Log raw SQL query untuk debugging
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        Log::info('Document query SQL', [
            'sql' => $sql,
            'bindings' => $bindings,
            'document_form_id' => $documentForm->id
        ]);
        
        // Periksa dokumen secara manual
        $allDocuments = Document::all();
        Log::info('All documents in database', [
            'total_count' => $allDocuments->count(),
            'documents_with_form_id' => $allDocuments->whereNotNull('document_form_id')->count(),
            'documents_with_this_form_id' => $allDocuments->where('document_form_id', $documentForm->id)->count(),
            'sample_document_ids' => $allDocuments->take(5)->pluck('id')->toArray(),
            'sample_document_form_ids' => $allDocuments->take(5)->pluck('document_form_id')->toArray()
        ]);
        
        // Tambahkan filter pencarian jika ada
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereJsonContains('metadata->name', $search)
                  ->orWhereJsonContains('metadata->city', $search)
                  ->orWhereJsonContains('metadata->whatsapp', $search);
            });
        }
        
        // Ambil dokumen dengan pagination
        $documents = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
            
        Log::info('DocumentForm paginated results', [
            'total_in_pagination' => $documents->total(),
            'has_pages' => $documents->hasPages(),
            'current_page' => $documents->currentPage(),
            'per_page' => $documents->perPage(),
            'status_counts' => $statusCounts
        ]);

        return Inertia::render('Admin/DocumentForms/Show', [
            'documentForm' => $documentForm,
            'documents' => $documents,
            'filters' => $request->only(['search', 'per_page']),
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentForm $documentForm)
    {
        $this->authorize('update', $documentForm);
        
        return Inertia::render('Admin/DocumentForms/Edit', [
            'documentForm' => $documentForm
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentForm $documentForm)
    {
        $this->authorize('update', $documentForm);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slug' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('document_forms', 'slug')->ignore($documentForm->id),
            ],
            'submission_deadline' => 'nullable|date',
            'closed_message' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'template_type' => 'required|string|in:default,article',
            'fields' => 'array'
        ], [
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Slug sudah digunakan, silakan gunakan slug yang lain.',
            'template_type.required' => 'Tipe template harus dipilih.',
            'template_type.in' => 'Tipe template tidak valid.'
        ]);
        
        $user = Auth::user();
        
        try {
            DB::beginTransaction();

            // Parse dan format submission_deadline jika ada
            if (isset($validated['submission_deadline'])) {
                $validated['submission_deadline'] = Carbon::parse($validated['submission_deadline']);
            }

            // Buat slug dari judul jika tidak ada
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['title']);
                
                // Pastikan slug unik
                $count = 0;
                $originalSlug = $validated['slug'];
                while (DocumentForm::where('slug', $validated['slug'])
                    ->where('id', '!=', $documentForm->id)
                    ->exists()
                ) {
                    $count++;
                    $validated['slug'] = $originalSlug . '-' . $count;
                }
            }
            
            // Update form dokumen
            $documentForm->update($validated);

            // Update form fields jika template berubah
            if ($request->template_type !== $documentForm->template_type) {
                // Hapus fields lama
                $documentForm->formFields()->delete();
                
                // Buat fields baru sesuai template
                $fields = DocumentForm::getDefaultFields($request->template_type);
                foreach ($fields as $field) {
                    $documentForm->formFields()->create($field);
                }
            }
            
            // Log aktivitas
            activity()
                ->performedOn($documentForm)
                ->causedBy($user)
                ->log('updated document form');
            
            DB::commit();

            return back()->with('success', 'Form dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating document form: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'data' => $validated,
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui form dokumen. ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentForm $documentForm)
    {
        try {
            DB::beginTransaction();
            
            // Periksa apakah ada dokumen yang terkait
            $documentsCount = Document::where('document_form_id', $documentForm->id)->count();
            if ($documentsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Form dokumen tidak dapat dihapus karena masih memiliki dokumen terkait.'
                ], 400);
            }
            
            // Log aktivitas sebelum menghapus
            activity()
                ->performedOn($documentForm)
                ->causedBy(auth()->user())
                ->log('deleted document form');
            
            // Hapus form dokumen
            $documentForm->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form dokumen berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting document form: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'document_form_id' => $documentForm->id,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus form dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the public URL for the form.
     */
    public function getPublicUrl(DocumentForm $documentForm)
    {
        $this->authorize('view', $documentForm);
        
        $publicUrl = url('/form/' . $documentForm->slug);
        
        return response()->json([
            'publicUrl' => $publicUrl
        ]);
    }
    
    /**
     * Clone the specified document form.
     */
    public function clone(DocumentForm $documentForm)
    {
        $this->authorize('create', DocumentForm::class);
        
        try {
            DB::beginTransaction();
            
            // Membuat salinan dari form yang ada
            $newForm = $documentForm->replicate();
            
            // Ubah judul untuk menunjukkan ini adalah hasil kloning
            $newForm->title = $documentForm->title . ' (Copy)';
            
            // Buat slug baru yang unik
            $originalSlug = Str::slug($newForm->title);
            $newForm->slug = $originalSlug;
            
            // Pastikan slug unik
            $count = 0;
            while (DocumentForm::where('slug', $newForm->slug)->exists()) {
                $count++;
                $newForm->slug = $originalSlug . '-' . $count;
            }
            
            // Set user_id ke pengguna saat ini
            $newForm->user_id = Auth::id();
            
            // Default ke status tidak aktif untuk form yang baru dikloning
            $newForm->is_active = false;
            
            // Simpan form baru
            $newForm->save();
            
            // Clone form fields
            foreach ($documentForm->formFields as $field) {
                $newField = $field->replicate();
                $newField->document_form_id = $newForm->id;
                $newField->save();
            }
            
            // Log aktivitas
            activity()
                ->performedOn($newForm)
                ->causedBy(Auth::user())
                ->log('cloned document form from ID: ' . $documentForm->id);
            
            DB::commit();
            
            // Kembalikan respons JSON alih-alih redirect
            return response()->json([
                'success' => true,
                'message' => 'Form dokumen berhasil dikloning.',
                'form_id' => $newForm->id
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cloning document form: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'document_form_id' => $documentForm->id,
            ]);
            
            // Kembalikan respons JSON dengan status error
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkloning form dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
} 
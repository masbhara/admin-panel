<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('media')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
        ]);
    }

    public function show($id)
    {
        $document = Document::with('media')->findOrFail($id);
        
        return Inertia::render('Admin/Documents/Show', [
            'document' => $document,
        ]);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Hapus semua media terkait
        foreach ($document->getMedia('document') as $media) {
            $media->delete();
        }
        
        $document->delete();
        
        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil dihapus');
    }
} 
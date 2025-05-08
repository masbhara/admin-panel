<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
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
            ]);

            if (!$request->hasFile('file')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'File dokumen wajib diupload']);
            }

            $file = $request->file('file');
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $document = Document::create([
                'name' => $request->name,
                'description' => 'Dokumen dari pengunjung: ' . $request->name,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
                'user_id' => 1, // Default admin user ID
                'uploaded_at' => now(),
                'status' => 'pending',
                'metadata' => [
                    'whatsapp' => $request->whatsapp,
                    'city' => $request->city,
                    'submitted_by' => 'guest'
                ],
            ]);

            return redirect()->back()->with('success', 'Dokumen berhasil dikirim! Kami akan menghubungi Anda segera.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
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

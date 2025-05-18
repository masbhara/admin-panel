<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DocumentSettingsController extends Controller
{
    public function index()
    {
        $documentSettings = DocumentSetting::first() ?? new DocumentSetting();
        $documentHomeTitle = Setting::where('key', 'document_home_title')->first();
        
        // Tidak perlu mengambil pengaturan lagi, karena sudah disediakan
        // secara global oleh AppServiceProvider ke semua halaman Inertia
        Log::info('DocumentSettingsController: Loading index page');
        
        return Inertia::render('Admin/DocumentSettings/Index', [
            'documentSettings' => $documentSettings,
            'document_home_title' => $documentHomeTitle ? $documentHomeTitle->value : 'Pengiriman Dokumen Online',
        ]);
    }

    public function update(Request $request)
    {
        Log::info('DocumentSettingsController: Received update request', $request->all());
        
        $validated = $request->validate([
            'submission_deadline' => 'required|date',
            'closed_message' => 'required|string|max:500',
            'is_active' => 'boolean',
            'document_home_title' => 'required|string|max:255',
        ]);

        $settings = DocumentSetting::first() ?? new DocumentSetting();
        
        // Parse dan format submission_deadline
        if (isset($validated['submission_deadline'])) {
            $validated['submission_deadline'] = Carbon::parse($validated['submission_deadline']);
            Log::info('DocumentSettingsController: Formatted submission_deadline for DB', ['formatted' => $validated['submission_deadline']]);
        }
        
        $settings->fill($validated);
        $settings->save();
        
        // Simpan judul home ke tabel settings
        Setting::updateOrCreate(
            ['key' => 'document_home_title'],
            [
                'value' => $validated['document_home_title'],
                'group' => 'document',
                'type' => 'text',
                'is_public' => true,
            ]
        );

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }

    public function getSettings()
    {
        $settings = DocumentSetting::first() ?? new DocumentSetting();
        $documentHomeTitle = Setting::where('key', 'document_home_title')->first();
        
        return response()->json([
            'submission_deadline' => $settings->formatted_submission_deadline,
            'closed_message' => $settings->closed_message,
            'is_active' => $settings->is_active,
            'document_home_title' => $documentHomeTitle ? $documentHomeTitle->value : 'Pengiriman Dokumen Online',
        ]);
    }
} 
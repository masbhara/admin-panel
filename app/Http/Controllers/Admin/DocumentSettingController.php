<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DocumentSettingController extends Controller
{
    public function index()
    {
        $settings = DocumentSetting::first() ?? new DocumentSetting();
        $documentHomeTitle = Setting::where('key', 'document_home_title')->first();
        \Log::info('DEBUG: submission_deadline data', [
            'raw' => $settings->submission_deadline,
            'formatted' => $settings->formatted_submission_deadline
        ]);
        return Inertia::render('Admin/DocumentSettings/Index', [
            'settings' => $settings,
            'document_home_title' => $documentHomeTitle ? $documentHomeTitle->value : 'Pengiriman Dokumen Online',
        ]);
    }

    public function update(Request $request)
    {
        \Log::info('DEBUG: Received update request', $request->all());
        
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
            \Log::info('DEBUG: Formatted submission_deadline for DB', ['formatted' => $validated['submission_deadline']]);
        }
        
        $settings->fill($validated);
        $settings->save();
        
        \Log::info('DEBUG: Settings after save', ['settings' => $settings->toArray()]);

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
        $documentHomeTitle = \App\Models\Setting::where('key', 'document_home_title')->first();
        \Log::info('DEBUG: submission_deadline data (API)', [
            'raw' => $settings->submission_deadline,
            'formatted' => $settings->formatted_submission_deadline
        ]);
        return response()->json([
            'submission_deadline' => $settings->formatted_submission_deadline,
            'closed_message' => $settings->closed_message,
            'is_active' => $settings->is_active,
            'document_home_title' => $documentHomeTitle ? $documentHomeTitle->value : 'Pengiriman Dokumen Online',
        ]);
    }
} 
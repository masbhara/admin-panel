<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentSettingController extends Controller
{
    public function index()
    {
        $settings = DocumentSetting::first() ?? new DocumentSetting();
        
        return Inertia::render('Admin/DocumentSettings/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'submission_deadline' => 'required|date',
            'closed_message' => 'required|string|max:500',
            'is_active' => 'boolean'
        ]);

        $settings = DocumentSetting::first() ?? new DocumentSetting();
        $settings->fill($validated);
        $settings->save();

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }

    public function getSettings()
    {
        $settings = DocumentSetting::first() ?? new DocumentSetting();
        
        return response()->json([
            'submission_deadline' => $settings->submission_deadline?->format('Y-m-d H:i:s'),
            'closed_message' => $settings->closed_message,
            'is_active' => $settings->is_active
        ]);
    }
} 
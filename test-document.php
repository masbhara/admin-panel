<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Models\DocumentForm;
use Illuminate\Support\Facades\Log;

// Cek apakah ada form dokumen
$documentForm = DocumentForm::first();
if (!$documentForm) {
    echo "Tidak ada form dokumen yang tersedia. Membuat form dokumen baru...\n";
    
    $documentForm = new DocumentForm();
    $documentForm->title = 'Form Dokumen Test';
    $documentForm->description = 'Form dokumen untuk testing';
    $documentForm->slug = 'test-form';
    $documentForm->is_active = true;
    $documentForm->user_id = 1; // Pastikan user dengan ID 1 ada
    $documentForm->save();
    
    echo "Form dokumen berhasil dibuat dengan ID: " . $documentForm->id . "\n";
} else {
    echo "Menggunakan form dokumen yang sudah ada dengan ID: " . $documentForm->id . "\n";
}

// Buat dokumen test
$doc = new Document();
$doc->name = 'Test Document';
$doc->description = 'Test Description';
$doc->file_path = 'public/documents/test.pdf';
$doc->file_name = 'test.pdf';
$doc->file_size = 1024;
$doc->file_type = 'application/pdf';
$doc->user_id = 1; // Pastikan user dengan ID 1 ada
$doc->document_form_id = $documentForm->id;
$doc->whatsapp_number = '08123456789';
$doc->status = 'pending';
$doc->uploaded_at = now();
$doc->metadata = [
    'name' => 'Test User',
    'whatsapp' => '08123456789',
    'city' => 'Test City',
    'form_title' => $documentForm->title,
    'form_slug' => $documentForm->slug,
];

try {
    $doc->save();
    echo "Dokumen berhasil dibuat dengan ID: " . $doc->id . "\n";
    
    // Verifikasi dokumen tersimpan
    $savedDoc = Document::find($doc->id);
    if ($savedDoc) {
        echo "Verifikasi: Dokumen ditemukan di database.\n";
        echo "Document Form ID: " . $savedDoc->document_form_id . "\n";
    } else {
        echo "Error: Dokumen tidak ditemukan di database setelah disimpan!\n";
    }
    
    // Log untuk debugging
    Log::info('Test document created', [
        'document_id' => $doc->id,
        'document_form_id' => $doc->document_form_id
    ]);
    
    // Hitung jumlah dokumen
    $docCount = Document::count();
    $docWithFormCount = Document::where('document_form_id', $documentForm->id)->count();
    
    echo "Total dokumen di database: " . $docCount . "\n";
    echo "Dokumen dengan form ID " . $documentForm->id . ": " . $docWithFormCount . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} 
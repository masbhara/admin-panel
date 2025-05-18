<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Ambil dokumen pertama
$doc = \App\Models\Document::first();
if ($doc) {
    echo "Dokumen ditemukan dengan ID: " . $doc->id . "\n";
    echo "Document form ID sebelumnya: " . ($doc->document_form_id ?: "NULL") . "\n";
    
    // Update document_form_id
    $doc->document_form_id = 1;
    $doc->save();
    
    echo "Document form ID setelah update: " . $doc->document_form_id . "\n";
    echo "Update berhasil!\n";
} else {
    echo "Tidak ada dokumen yang ditemukan.\n";
}

// Periksa relasi
$documentForm = \App\Models\DocumentForm::find(1);
if ($documentForm) {
    echo "Form dokumen ditemukan dengan ID: " . $documentForm->id . "\n";
    echo "Jumlah dokumen terkait: " . $documentForm->documents()->count() . "\n";
} else {
    echo "Form dokumen dengan ID 1 tidak ditemukan.\n";
} 
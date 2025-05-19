<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Models\DocumentForm;
use Illuminate\Support\Facades\DB;

// Periksa jumlah dokumen
$documentCount = Document::count();
echo "Total dokumen: {$documentCount}\n";

// Periksa jumlah form dokumen
$formCount = DocumentForm::count();
echo "Total form dokumen: {$formCount}\n";

// Periksa dokumen terkait form
$forms = DocumentForm::all();
foreach ($forms as $form) {
    $docCount = Document::where('document_form_id', $form->id)->count();
    echo "Form '{$form->title}' (ID: {$form->id}) memiliki {$docCount} dokumen\n";
}

// Periksa struktur tabel dokumen
$columns = DB::select("SHOW COLUMNS FROM documents WHERE Field = 'document_form_id'");
if (!empty($columns)) {
    $column = $columns[0];
    echo "Struktur kolom document_form_id:\n";
    echo "  Field: {$column->Field}\n";
    echo "  Type: {$column->Type}\n";
    echo "  Null: {$column->Null}\n";
    echo "  Key: {$column->Key}\n";
    echo "  Default: " . ($column->Default ?? "NULL") . "\n";
    echo "  Extra: {$column->Extra}\n";
}

// Periksa foreign key
$foreignKeys = DB::select("
    SELECT *
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'document_form_id'
    AND REFERENCED_TABLE_NAME IS NOT NULL
");

if (!empty($foreignKeys)) {
    $fk = $foreignKeys[0];
    echo "Foreign key untuk document_form_id:\n";
    echo "  Constraint: {$fk->CONSTRAINT_NAME}\n";
    echo "  Referenced Table: {$fk->REFERENCED_TABLE_NAME}\n";
    echo "  Referenced Column: {$fk->REFERENCED_COLUMN_NAME}\n";
} else {
    echo "Tidak ada foreign key untuk document_form_id\n";
}

// Periksa dokumen tanpa form_id
$docsWithoutFormId = Document::whereNull('document_form_id')->count();
echo "Dokumen tanpa document_form_id: {$docsWithoutFormId}\n";

// Periksa dokumen dengan form_id yang tidak valid
$invalidFormDocs = DB::select("
    SELECT COUNT(*) as count
    FROM documents d
    LEFT JOIN document_forms df ON d.document_form_id = df.id
    WHERE df.id IS NULL
");

echo "Dokumen dengan document_form_id tidak valid: {$invalidFormDocs[0]->count}\n"; 
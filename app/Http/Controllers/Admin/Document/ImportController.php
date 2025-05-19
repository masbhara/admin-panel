<?php

namespace App\Http\Controllers\Admin\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentForm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class ImportController extends Controller
{
    /**
     * Import dokumen dari file Excel/CSV
     */
    public function import(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Maksimal 10MB
            'document_form_id' => 'required|exists:document_forms,id'
        ]);

        if ($validator->fails()) {
            Log::error('Validasi import dokumen gagal', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            // Dapatkan document form
            $documentForm = DocumentForm::findOrFail($request->document_form_id);
            
            // Proses file
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'import_' . time() . '.' . $extension;
            $filePath = $file->storeAs('temp', $fileName);
            $fullPath = storage_path('app/' . $filePath);
            
            // Baca file
            $reader = IOFactory::createReaderForFile($fullPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $rows = [];
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $rows[] = $rowData;
            }
            
            // Hapus baris header
            if (count($rows) > 0) {
                array_shift($rows);
            }
            
            // Import data
            $imported = 0;
            $failed = 0;
            $errors = [];
            
            foreach ($rows as $index => $row) {
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }
                
                try {
                    // Pastikan ada minimal 3 kolom (nama, whatsapp, city)
                    if (count($row) < 3) {
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                        $failed++;
                        continue;
                    }
                    
                    $name = $row[0] ?? null;
                    $whatsapp = $row[1] ?? null;
                    $city = $row[2] ?? null;
                    $additionalData = [];
                    
                    // Kolom tambahan jika ada
                    for ($i = 3; $i < count($row); $i++) {
                        $additionalData['custom_field_' . ($i - 2)] = $row[$i];
                    }
                    
                    // Validasi data
                    if (empty($name)) {
                        $errors[] = "Baris " . ($index + 2) . ": Nama tidak boleh kosong";
                        $failed++;
                        continue;
                    }
                    
                    // Simpan dokumen
                    $document = new Document();
                    $document->name = $name;
                    $document->document_form_id = $documentForm->id;
                    $document->uploaded_at = now();
                    $document->status = 'pending'; // default status
                    
                    // Set metadata
                    $metadata = [
                        'name' => $name,
                        'whatsapp' => $whatsapp,
                        'city' => $city
                    ];
                    
                    // Tambahkan data tambahan jika ada
                    if (!empty($additionalData)) {
                        $metadata = array_merge($metadata, $additionalData);
                    }
                    
                    $document->metadata = $metadata;
                    $document->save();
                    
                    $imported++;
                } catch (\Exception $e) {
                    Log::error('Error saat import dokumen baris ' . ($index + 2), [
                        'error' => $e->getMessage(),
                        'data' => $row
                    ]);
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    $failed++;
                }
            }
            
            // Hapus file temporary
            Storage::delete($filePath);
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$imported} dokumen" . ($failed > 0 ? ", gagal {$failed} dokumen" : ""),
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors
            ]);
        } catch (ReaderException $e) {
            Log::error('Error saat membaca file import', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Format file tidak valid atau rusak: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat import dokumen', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 
<?php

namespace App\Http\Controllers\Admin\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        try {
            // Pastikan direktori imported ada
            if (!Storage::exists('public/imported')) {
                Storage::makeDirectory('public/imported');
            }
            
            // Log permulaan import dengan detail request
            Log::info('=== MULAI PROSES IMPORT CSV ===', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Validasi file dengan custom messages
            $validator = Validator::make($request->all(), [
                'file' => [
                    'required',
                    'file',
                    'mimes:csv,txt',
                    'max:5120', // Max 5MB
                ],
            ], [
                'file.required' => 'File CSV wajib diunggah.',
                'file.file' => 'Upload harus berupa file yang valid.',
                'file.mimes' => 'Format file tidak valid. Hanya file CSV atau TXT yang diperbolehkan.',
                'file.max' => 'Ukuran file terlalu besar. Maksimal 5MB.',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi import gagal', [
                    'errors' => $validator->errors()->toArray()
                ]);
                throw new ValidationException($validator);
            }
            
            $file = $request->file('file');
            
            // Verifikasi file secara manual
            if (!$file || !$file->isValid()) {
                Log::error('File tidak valid atau kosong');
                throw ValidationException::withMessages([
                    'file' => ['File tidak valid atau kosong.']
                ]);
            }
            
            // Log informasi file
            $filePath = $file->getRealPath();
            $fileType = $file->getMimeType();
            $fileSize = $file->getSize();
            $extension = strtolower($file->getClientOriginalExtension());
            
            Log::info("Detail File CSV:", [
                'name' => $file->getClientOriginalName(),
                'size' => $fileSize,
                'type' => $fileType,
                'extension' => $extension,
                'path' => $filePath
            ]);
            
            // Set batas waktu eksekusi yang lebih lama untuk file besar
            set_time_limit(600); // 10 menit
            
            // Cek apakah file bisa dibaca
            if (!is_readable($filePath)) {
                Log::error('File tidak dapat dibaca', ['path' => $filePath]);
                throw ValidationException::withMessages([
                    'file' => ['File tidak dapat dibaca. Pastikan file tidak rusak.']
                ]);
            }
            
            // Validasi format CSV
            try {
                $fileHandle = fopen($filePath, 'r');
                if ($fileHandle === false) {
                    throw new \Exception('Tidak dapat membuka file');
                }
                
                // Coba baca header
                $header = fgetcsv($fileHandle, 0, ',', '"');
                if ($header === false || count($header) < 2) {
                    fclose($fileHandle);
                    Log::error('Format CSV tidak valid', ['header' => $header]);
                    throw ValidationException::withMessages([
                        'file' => ['Format file CSV tidak valid. Pastikan file memiliki header dan data yang benar.']
                    ]);
                }
                
                // Validasi header minimal harus ada kolom nama
                $hasNameColumn = false;
                foreach ($header as $columnName) {
                    $columnName = strtolower(trim($columnName));
                    if (in_array($columnName, ['nama dokumen', 'nama', 'judul'])) {
                        $hasNameColumn = true;
                        break;
                    }
                }
                
                if (!$hasNameColumn) {
                    fclose($fileHandle);
                    Log::error('Header CSV tidak memiliki kolom nama');
                    throw ValidationException::withMessages([
                        'file' => ['File CSV harus memiliki kolom Nama Dokumen/Nama/Judul.']
                    ]);
                }
                
                fclose($fileHandle);
                Log::info('Validasi format CSV berhasil', ['columns' => count($header)]);
                
            } catch (\Exception $e) {
                Log::error('Error membaca file CSV', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw ValidationException::withMessages([
                    'file' => ['Gagal membaca file: ' . $e->getMessage()]
                ]);
            }
            
            // Mulai transaksi database
            DB::beginTransaction();
            
            try {
                // Pilih metode import berdasarkan ukuran file
                if ($fileSize > 1 * 1024 * 1024) { // > 1MB
                    Log::info('Menggunakan metode import file besar');
                    $result = $this->importLargeCSV($file);
                } else {
                    Log::info('Menggunakan metode import file kecil');
                    $result = $this->importSmallCSV($file);
                }
                
                if ($result['imported'] > 0) {
                    DB::commit();
                    Log::info('Import CSV berhasil', [
                        'total_imported' => $result['imported'],
                        'errors' => $result['errors']
                    ]);
                    
                    return redirect()
                        ->back()
                        ->with('success', $result['message'])
                        ->with('importErrors', $result['errors'] ?? []);
                } else {
                    DB::rollBack();
                    Log::warning('Import CSV tidak ada data yang diimport');
                    throw ValidationException::withMessages([
                        'file' => ['Tidak ada data yang berhasil diimport. Periksa format file CSV Anda.']
                    ]);
                }
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error dalam proses import', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                throw ValidationException::withMessages([
                    'file' => ['Terjadi kesalahan saat import: ' . $e->getMessage()]
                ]);
            }
            
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error tidak terduga dalam import', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withErrors(['file' => 'Terjadi kesalahan yang tidak terduga: ' . $e->getMessage()])
                ->withInput();
        }
    }
    
    /**
     * Import file CSV kecil menggunakan PhpSpreadsheet
     */
    private function importSmallCSV($file)
    {
        try {
            $filePath = $file->getRealPath();
            
            Log::info('=== MULAI IMPORT FILE KECIL ===');
            Log::info('File: ' . $file->getClientOriginalName());
            
            // Baca file CSV
            $fileHandle = fopen($filePath, 'r');
            if ($fileHandle === false) {
                throw new \Exception('Tidak dapat membuka file untuk dibaca');
            }
            
            // Baca header
            $header = fgetcsv($fileHandle);
            if ($header === false) {
                fclose($fileHandle);
                throw new \Exception('Tidak dapat membaca header CSV');
            }
            
            Log::info('Header CSV:', ['columns' => $header]);
            
            // Mapping kolom dengan header
            $columnMap = [];
            foreach ($header as $index => $columnName) {
                if (empty($columnName)) continue;
                
                $columnName = strtolower(trim($columnName));
                
                if (in_array($columnName, ['nama dokumen', 'nama', 'judul'])) {
                    $columnMap['name'] = $index;
                } elseif (in_array($columnName, ['deskripsi', 'keterangan'])) {
                    $columnMap['description'] = $index;
                } elseif (in_array($columnName, ['pengirim', 'nama pengirim'])) {
                    $columnMap['pengirim'] = $index;
                } elseif (in_array($columnName, ['whatsapp', 'no whatsapp', 'nomor whatsapp', 'telepon', 'hp'])) {
                    $columnMap['whatsapp'] = $index;
                } elseif (in_array($columnName, ['asal kota', 'kota', 'kota asal'])) {
                    $columnMap['city'] = $index;
                } elseif (in_array($columnName, ['status'])) {
                    $columnMap['status'] = $index;
                }
            }
            
            Log::info('Column mapping:', $columnMap);
            
            // Validasi header minimal harus ada kolom nama
            if (!isset($columnMap['name'])) {
                fclose($fileHandle);
                throw new \Exception('Format file tidak valid. Kolom Nama Dokumen wajib ada.');
            }
            
            $imported = 0;
            $errors = [];
            $batch = [];
            $batchSize = 100;
            $rowNum = 1;
            
            // Proses baris per baris
            while (($row = fgetcsv($fileHandle)) !== false) {
                $rowNum++;
                
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }
                
                Log::info("Memproses baris {$rowNum}:", ['data' => $row]);
                
                $name = isset($columnMap['name']) && isset($row[$columnMap['name']]) ? 
                        trim($row[$columnMap['name']]) : null;
                
                // Validasi nama tidak boleh kosong
                if (empty($name)) {
                    $errors[] = "Baris {$rowNum}: Nama dokumen tidak boleh kosong.";
                    continue;
                }
                
                // Siapkan metadata
                $metadata = [];
                
                // Tambahkan metadata pengirim jika ada
                if (isset($columnMap['pengirim']) && isset($row[$columnMap['pengirim']]) && !empty($row[$columnMap['pengirim']])) {
                    $metadata['pengirim'] = trim($row[$columnMap['pengirim']]);
                }
                
                // Tambahkan metadata whatsapp jika ada
                if (isset($columnMap['whatsapp']) && isset($row[$columnMap['whatsapp']]) && !empty($row[$columnMap['whatsapp']])) {
                    $metadata['whatsapp'] = trim($row[$columnMap['whatsapp']]);
                }
                
                // Tambahkan metadata kota jika ada
                if (isset($columnMap['city']) && isset($row[$columnMap['city']]) && !empty($row[$columnMap['city']])) {
                    $metadata['city'] = trim($row[$columnMap['city']]);
                }
                
                // Tentukan status (default: pending)
                $status = 'pending';
                if (isset($columnMap['status']) && isset($row[$columnMap['status']]) && !empty($row[$columnMap['status']])) {
                    $statusValue = strtolower(trim($row[$columnMap['status']]));
                    if (in_array($statusValue, ['pending', 'approved', 'rejected'])) {
                        $status = $statusValue;
                    }
                }
                
                // Siapkan data dokumen
                $documentData = [
                    'name' => $name,
                    'description' => isset($columnMap['description']) && isset($row[$columnMap['description']]) ? 
                                    trim($row[$columnMap['description']]) : null,
                    'user_id' => auth()->id(),
                    'file_path' => 'imported/no-file.txt', // Default path untuk dokumen imported
                    'file_name' => $name . '.txt', // Gunakan nama dokumen sebagai nama file
                    'file_size' => 0, // Size 0 untuk dokumen tanpa file
                    'file_type' => 'text/plain', // Default type untuk dokumen imported
                    'uploaded_at' => Carbon::now(),
                    'status' => $status,
                    'metadata' => json_encode($metadata),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                Log::info("Data dokumen yang akan diinsert:", $documentData);
                
                try {
                    // Insert langsung per baris untuk debugging
                    Document::create($documentData);
                    $imported++;
                    
                    Log::info("Berhasil insert dokumen: {$name}");
                } catch (\Exception $e) {
                    Log::error("Gagal insert dokumen: {$name}", [
                        'error' => $e->getMessage(),
                        'data' => $documentData
                    ]);
                    $errors[] = "Baris {$rowNum}: Gagal menyimpan dokumen - " . $e->getMessage();
                    continue;
                }
            }
            
            fclose($fileHandle);
            
            Log::info('=== IMPORT SELESAI ===', [
                'total_imported' => $imported,
                'total_errors' => count($errors)
            ]);
            
            return [
                'message' => "Berhasil mengimpor {$imported} dokumen" . 
                            (!empty($errors) ? ". Terdapat " . count($errors) . " baris yang dilewati karena error." : "."),
                'errors' => $errors,
                'imported' => $imported
            ];
            
        } catch (\Exception $e) {
            Log::error('Error dalam importSmallCSV: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
    
    /**
     * Import file CSV besar dengan pendekatan streaming
     */
    private function importLargeCSV($file) 
    {
        try {
            $filePath = $file->getRealPath();
            
            Log::info('Memulai import file besar: ' . $file->getClientOriginalName());
            
            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception('Tidak dapat membuka file untuk dibaca');
            }
            
            // Baca header
            $header = fgetcsv($handle, 0, ',', '"');
            if ($header === false) {
                fclose($handle);
                throw new \Exception('Tidak dapat membaca header CSV');
            }
            
            // Mapping kolom dengan header
            $columnMap = [];
            foreach ($header as $index => $columnName) {
                if (empty($columnName)) continue;
                
                $columnName = strtolower(trim($columnName));
                
                if (in_array($columnName, ['nama dokumen', 'nama', 'judul'])) {
                    $columnMap['name'] = $index;
                } elseif (in_array($columnName, ['deskripsi', 'keterangan'])) {
                    $columnMap['description'] = $index;
                } elseif (in_array($columnName, ['pengirim', 'nama pengirim'])) {
                    $columnMap['pengirim'] = $index;
                } elseif (in_array($columnName, ['whatsapp', 'no whatsapp', 'nomor whatsapp', 'telepon', 'hp'])) {
                    $columnMap['whatsapp'] = $index;
                } elseif (in_array($columnName, ['asal kota', 'kota', 'kota asal'])) {
                    $columnMap['city'] = $index;
                } elseif (in_array($columnName, ['status'])) {
                    $columnMap['status'] = $index;
                }
            }
            
            // Validasi header minimal harus ada kolom nama
            if (!isset($columnMap['name'])) {
                fclose($handle);
                throw new \Exception('Format file tidak valid. Kolom Nama Dokumen wajib ada.');
            }
            
            $batchSize = 100;
            $batch = [];
            $imported = 0;
            $errors = [];
            $rowNum = 1;
            
            // Proses baris per baris
            while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
                $rowNum++;
                
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }
                
                $name = isset($columnMap['name']) && isset($row[$columnMap['name']]) ? 
                        trim($row[$columnMap['name']]) : null;
                
                // Validasi nama tidak boleh kosong
                if (empty($name)) {
                    $errors[] = "Baris {$rowNum}: Nama dokumen tidak boleh kosong.";
                    continue;
                }
                
                // Siapkan metadata
                $metadata = [];
                
                // Tambahkan metadata pengirim jika ada
                if (isset($columnMap['pengirim']) && isset($row[$columnMap['pengirim']]) && !empty($row[$columnMap['pengirim']])) {
                    $metadata['pengirim'] = trim($row[$columnMap['pengirim']]);
                }
                
                // Tambahkan metadata whatsapp jika ada
                if (isset($columnMap['whatsapp']) && isset($row[$columnMap['whatsapp']]) && !empty($row[$columnMap['whatsapp']])) {
                    $metadata['whatsapp'] = trim($row[$columnMap['whatsapp']]);
                }
                
                // Tambahkan metadata kota jika ada
                if (isset($columnMap['city']) && isset($row[$columnMap['city']]) && !empty($row[$columnMap['city']])) {
                    $metadata['city'] = trim($row[$columnMap['city']]);
                }
                
                // Tentukan status (default: pending)
                $status = 'pending';
                if (isset($columnMap['status']) && isset($row[$columnMap['status']]) && !empty($row[$columnMap['status']])) {
                    $statusValue = strtolower(trim($row[$columnMap['status']]));
                    if (in_array($statusValue, ['pending', 'approved', 'rejected'])) {
                        $status = $statusValue;
                    }
                }
                
                // Siapkan data dokumen
                $documentData = [
                    'name' => $name,
                    'description' => isset($columnMap['description']) && isset($row[$columnMap['description']]) ? 
                                    trim($row[$columnMap['description']]) : null,
                    'user_id' => auth()->id(),
                    'file_path' => 'imported/no-file.txt', // Default path untuk dokumen imported
                    'file_name' => $name . '.txt', // Gunakan nama dokumen sebagai nama file
                    'file_size' => 0, // Size 0 untuk dokumen tanpa file
                    'file_type' => 'text/plain', // Default type untuk dokumen imported
                    'uploaded_at' => Carbon::now(),
                    'status' => $status,
                    'metadata' => json_encode($metadata),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Tambahkan ke batch
                $batch[] = $documentData;
                $imported++;
                
                // Jika batch sudah mencapai ukuran tertentu, insert ke database
                if (count($batch) >= $batchSize) {
                    try {
                        Document::insert($batch);
                        Log::info("Berhasil insert batch dokumen, jumlah: " . count($batch));
                    } catch (\Exception $e) {
                        Log::error("Gagal insert batch dokumen", [
                            'error' => $e->getMessage(),
                            'batch_size' => count($batch)
                        ]);
                        // Coba insert satu per satu untuk menangani kasus partial failure
                        foreach ($batch as $data) {
                            try {
                                Document::create($data);
                                Log::info("Berhasil insert dokumen tunggal: {$data['name']}");
                            } catch (\Exception $innerE) {
                                Log::error("Gagal insert dokumen tunggal: {$data['name']}", [
                                    'error' => $innerE->getMessage()
                                ]);
                                $errors[] = "Gagal menyimpan dokumen {$data['name']} - " . $innerE->getMessage();
                                $imported--; // Kurangi counter karena gagal
                            }
                        }
                    }
                    $batch = [];
                    
                    // Sedikit jeda untuk mengurangi beban CPU
                    usleep(10000); // 10ms
                }
            }
            
            // Proses sisa batch
            if (!empty($batch)) {
                try {
                    Document::insert($batch);
                    Log::info("Berhasil insert sisa batch dokumen, jumlah: " . count($batch));
                } catch (\Exception $e) {
                    Log::error("Gagal insert sisa batch dokumen", [
                        'error' => $e->getMessage(),
                        'batch_size' => count($batch)
                    ]);
                    // Coba insert satu per satu untuk menangani kasus partial failure
                    foreach ($batch as $data) {
                        try {
                            Document::create($data);
                            Log::info("Berhasil insert dokumen tunggal: {$data['name']}");
                        } catch (\Exception $innerE) {
                            Log::error("Gagal insert dokumen tunggal: {$data['name']}", [
                                'error' => $innerE->getMessage()
                            ]);
                            $errors[] = "Gagal menyimpan dokumen {$data['name']} - " . $innerE->getMessage();
                            $imported--; // Kurangi counter karena gagal
                        }
                    }
                }
            }
            
            // Tutup file
            fclose($handle);
            
            // Log aktivitas
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['count' => $imported])
                ->log('imported documents from large CSV');
            
            return [
                'message' => "Berhasil mengimpor {$imported} dokumen" . 
                            (!empty($errors) ? ". Terdapat " . count($errors) . " baris yang dilewati karena error." : "."),
                'errors' => $errors,
                'imported' => $imported
            ];
            
        } catch (\Exception $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            Log::error('Error dalam importLargeCSV: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
} 
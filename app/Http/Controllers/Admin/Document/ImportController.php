<?php

namespace App\Http\Controllers\Admin\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        try {
            // Log permulaan import
            Log::info('Memulai proses import CSV');
            
            // Validasi file
            $validationRules = [
                'file' => [
                    'required',
                    'file',
                    'max:5120', // Max 5MB
                ],
            ];
            
            // Jalankan validasi
            $request->validate($validationRules);
            
            $file = $request->file('file');
            
            // Verifikasi file secara manual
            if (!$file || !$file->isValid()) {
                Log::error('File tidak valid atau kosong');
                return redirect()->back()->withErrors(['file' => 'File tidak valid atau kosong.']);
            }
            
            // Cek ekstensi file secara manual dengan berbagai kemungkinan
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['csv', 'txt'])) {
                Log::error('Ekstensi file tidak valid: ' . $extension);
                return redirect()->back()->withErrors(['file' => 'Format file tidak didukung. Hanya CSV atau TXT yang diperbolehkan.']);
            }
            
            $filePath = $file->getRealPath();
            $fileType = $file->getMimeType();
            $fileSize = $file->getSize();
            
            // Log informasi file dengan lebih detail
            Log::info("File CSV diterima: {$file->getClientOriginalName()} ($fileSize bytes)");
            Log::info("MIME Type: $fileType, Extension: $extension");
            
            // Set batas waktu eksekusi yang lebih lama untuk file besar
            set_time_limit(600); // 10 menit
            
            // Cek apakah file bisa dibaca
            if (!is_readable($filePath)) {
                Log::error('File tidak dapat dibaca: ' . $filePath);
                return redirect()->back()->withErrors(['file' => 'File tidak dapat dibaca.']);
            }
            
            // Coba baca file terlebih dahulu untuk memastikan formatnya benar
            try {
                $fileHandle = fopen($filePath, 'r');
                if ($fileHandle === false) {
                    throw new \Exception('Tidak dapat membuka file');
                }
                
                // Coba baca baris pertama (header)
                $header = fgetcsv($fileHandle, 0, ',', '"');
                if ($header === false || count($header) < 2) {
                    fclose($fileHandle);
                    Log::error('Format file CSV tidak valid atau kosong');
                    return redirect()->back()->withErrors(['file' => 'Format file CSV tidak valid atau kosong. Pastikan file memiliki format yang benar.']);
                }
                
                fclose($fileHandle);
                Log::info('File CSV valid, memiliki ' . count($header) . ' kolom');
                
            } catch (\Exception $e) {
                Log::error('Error membaca file CSV: ' . $e->getMessage());
                return redirect()->back()->withErrors(['file' => 'Gagal membaca file: ' . $e->getMessage()]);
            }
            
            // Lanjutkan dengan proses import yang sesuai berdasarkan ukuran file
            if ($fileSize > 1 * 1024 * 1024) { // Jika lebih dari 1MB
                Log::info('Menggunakan metode import file besar untuk ' . $file->getClientOriginalName());
                return $this->importLargeCSV($file);
            }
            
            // Untuk file kecil, gunakan metode PhpSpreadsheet
            return $this->importSmallCSV($file);
            
        } catch (\Exception $e) {
            Log::error('Error dalam proses import: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Import file CSV kecil menggunakan PhpSpreadsheet
     */
    private function importSmallCSV($file)
    {
        try {
            $filePath = $file->getRealPath();
            
            Log::info('Memulai import file kecil: ' . $file->getClientOriginalName());
            
            // Gunakan pembacaan CSV yang lebih efisien
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
                $reader->setDelimiter(',');
                $reader->setEnclosure('"');
                $reader->setSheetIndex(0);
                $reader->setReadDataOnly(true); // Hanya baca data, abaikan formatting
                
                Log::info('Mencoba membaca file CSV dengan PhpSpreadsheet');
                $spreadsheet = $reader->load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, false, true);
                
                Log::info('Berhasil membaca file CSV. Jumlah baris: ' . count($rows));
                
                // Log contoh beberapa baris pertama untuk debugging
                if (count($rows) > 0) {
                    Log::info('Contoh header: ' . json_encode(reset($rows)));
                    
                    // Log sampel baris data jika ada
                    if (count($rows) > 1) {
                        $sampleDataRow = array_values($rows)[1] ?? null;
                        if ($sampleDataRow) {
                            Log::info('Contoh baris data: ' . json_encode($sampleDataRow));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error saat membaca file CSV: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return redirect()->back()->withErrors(['file' => 'Gagal membaca file CSV: ' . $e->getMessage()]);
            }
            
            // Header ada di baris pertama
            $header = array_shift($rows);
            Log::info('Header CSV: ' . json_encode($header));
            
            // Mapping kolom dengan header
            $columnMap = [];
            foreach ($header as $column => $columnName) {
                if (empty($columnName)) continue;
                
                $columnName = strtolower(trim($columnName));
                
                if (in_array($columnName, ['nama dokumen', 'nama', 'judul'])) {
                    $columnMap['name'] = $column;
                } elseif (in_array($columnName, ['deskripsi', 'keterangan'])) {
                    $columnMap['description'] = $column;
                } elseif (in_array($columnName, ['pengirim', 'nama pengirim'])) {
                    $columnMap['pengirim'] = $column;
                } elseif (in_array($columnName, ['whatsapp', 'no whatsapp', 'nomor whatsapp', 'telepon', 'hp'])) {
                    $columnMap['whatsapp'] = $column;
                } elseif (in_array($columnName, ['asal kota', 'kota', 'kota asal'])) {
                    $columnMap['city'] = $column;
                } elseif (in_array($columnName, ['status'])) {
                    $columnMap['status'] = $column;
                }
            }
            
            Log::info('Column mapping: ' . json_encode($columnMap));
            
            // Validasi header minimal harus ada kolom nama
            if (!isset($columnMap['name'])) {
                Log::warning('Import gagal: Tidak ada kolom Nama Dokumen');
                return back()->withErrors(['file' => 'Format file tidak valid. Kolom Nama Dokumen wajib ada.']);
            }
            
            // Gunakan transaksi database untuk efisiensi
            Log::info('Memulai transaksi database');
            DB::beginTransaction();
            
            try {
                $imported = 0;
                $errors = [];
                
                // Siapkan batch untuk insert yang lebih efisien
                $batchSize = 100;
                $batch = [];
                
                foreach ($rows as $rowIndex => $row) {
                    $rowNum = $rowIndex + 1; // +1 karena array dimulai dari indexing alfanumerik dalam Excel
                    
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
                        'file_path' => null, // Placeholder untuk dokumen tanpa file
                        'file_name' => null,
                        'file_size' => 0,
                        'file_type' => null,
                        'uploaded_at' => Carbon::now(),
                        'status' => $status,
                        'metadata' => $metadata,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    // Tambahkan ke batch
                    $batch[] = $documentData;
                    $imported++;
                    
                    // Jika batch sudah mencapai ukuran tertentu, insert ke database
                    if (count($batch) >= $batchSize) {
                        try {
                            $this->handleBatchImport($batch);
                            $batch = [];
                        } catch (\Exception $e) {
                            Log::error('Error saat inserting batch: ' . $e->getMessage());
                            Log::error('Data batch yang gagal: ' . json_encode(array_slice($batch, 0, 2)) . '...');
                            throw $e;
                        }
                    }
                }
                
                // Insert sisa data yang belum di-batch
                if (!empty($batch)) {
                    try {
                        $this->handleBatchImport($batch);
                    } catch (\Exception $e) {
                        Log::error('Error saat inserting remaining batch: ' . $e->getMessage());
                        Log::error('Data batch yang gagal: ' . json_encode(array_slice($batch, 0, 2)) . '...');
                        throw $e;
                    }
                }
                
                Log::info('Commit transaksi database');
                DB::commit();
                
                // Log aktivitas
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['count' => $imported])
                    ->log('imported documents from CSV');
                
                $message = "Berhasil mengimpor {$imported} dokumen.";
                if (!empty($errors)) {
                    $message .= " Terdapat " . count($errors) . " baris yang dilewati karena error.";
                    
                    // Tambahkan detail error ke session flash untuk ditampilkan
                    session()->flash('importErrors', $errors);
                }
                
                Log::info($message);
                return redirect()->route('admin.documents.index')->with('success', $message);
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error dalam import CSV (kecil): ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan saat import: ' . $e->getMessage()]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error dalam importSmallCSV: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan umum: ' . $e->getMessage()]);
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
                Log::warning('Import gagal: Tidak ada kolom Nama Dokumen di file besar');
                return back()->withErrors(['file' => 'Format file tidak valid. Kolom Nama Dokumen wajib ada.']);
            }
            
            // Mulai transaksi database
            DB::beginTransaction();
            
            try {
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
                        'file_path' => null, // Placeholder untuk dokumen tanpa file
                        'file_name' => null,
                        'file_size' => 0,
                        'file_type' => null,
                        'uploaded_at' => Carbon::now(),
                        'status' => $status,
                        'metadata' => $metadata,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    // Tambahkan ke batch
                    $batch[] = $documentData;
                    $imported++;
                    
                    // Jika batch sudah mencapai ukuran tertentu, insert ke database
                    if (count($batch) >= $batchSize) {
                        $this->handleBatchImport($batch);
                        $batch = [];
                        
                        // Sedikit jeda untuk mengurangi beban CPU
                        usleep(10000); // 10ms
                    }
                }
                
                // Proses sisa batch
                if (!empty($batch)) {
                    $this->handleBatchImport($batch);
                }
                
                // Commit transaksi
                DB::commit();
                
                // Tutup file
                fclose($handle);
                
                // Log aktivitas
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['count' => $imported])
                    ->log('imported documents from large CSV');
                
                $message = "Berhasil mengimpor {$imported} dokumen.";
                if (!empty($errors)) {
                    $message .= " Terdapat " . count($errors) . " baris yang dilewati karena error.";
                    
                    // Tambahkan detail error ke session flash untuk ditampilkan
                    session()->flash('importErrors', $errors);
                }
                
                Log::info($message);
                return redirect()->route('admin.documents.index')->with('success', $message);
                
            } catch (\Exception $e) {
                DB::rollBack();
                if (isset($handle) && is_resource($handle)) {
                    fclose($handle);
                }
                
                Log::error('Error dalam import CSV (besar): ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan saat import file besar: ' . $e->getMessage()]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error dalam importLargeCSV: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan umum dalam import file besar: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Menangani insert batch dokumen ke database
     */
    private function handleBatchImport(array $batch)
    {
        // Persiapkan metadata untuk database
        foreach ($batch as &$document) {
            if (isset($document['metadata'])) {
                $document['metadata'] = json_encode($document['metadata']);
            }
        }
        
        // Insert batch
        Document::insert($batch);
        
        return true;
    }
} 
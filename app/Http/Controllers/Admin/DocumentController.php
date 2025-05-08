<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Document;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        $documents = Document::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->with('user') // Assuming documents are linked to users
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Documents/Create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'required|file|max:10240', // Max 10MB
                'metadata' => 'nullable|array',
            ]);

            if (!$request->hasFile('file')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'File dokumen wajib diupload']);
            }

            $file = $request->file('file');
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Pastikan file disimpan di public/documents, bukan documents
            $filePath = $file->storeAs('public/documents', $fileName);
            
            // Log untuk debugging
            Log::info('Storing new document: ' . $fileName . ' at path: ' . $filePath);

            $document = Document::create([
                'name' => $request->name,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
                'user_id' => auth()->id(),
                'uploaded_at' => Carbon::now(),
                'status' => 'pending',
                'metadata' => $request->metadata,
            ]);

            // Log activity
            activity()
                ->performedOn($document)
                ->causedBy(auth()->user())
                ->withProperties(['name' => $document->name])
                ->log('created document');

            return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error storing document: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show(Document $document)
    {
        $activities = Activity::where('subject_type', Document::class)
            ->where('subject_id', $document->id)
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Admin/Documents/Show', [
            'document' => $document->load('user'),
            'activities' => $activities,
        ]);
    }

    public function edit(Document $document)
    {
        return Inertia::render('Admin/Documents/Edit', [
            'document' => $document,
        ]);
    }

    public function update(Request $request, Document $document)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'nullable|file|max:10240', // Max 10MB
                'status' => 'required|in:pending,approved,rejected',
                'metadata' => 'nullable|array',
            ]);

            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'metadata' => $request->metadata,
            ];

            // Jika ada file baru yang diunggah
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Hapus file lama jika ada
                if ($document->file_path && Storage::exists('public/' . $document->file_path)) {
                    Storage::delete('public/' . $document->file_path);
                }
                
                $filePath = $file->storeAs('documents', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_size'] = $file->getSize();
                $data['file_type'] = $file->getClientMimeType();
            }

            $document->update($data);

            // Log activity
            activity()
                ->performedOn($document)
                ->causedBy(auth()->user())
                ->withProperties([
                    'name' => $document->name,
                    'status' => $document->status,
                    'file_updated' => $request->hasFile('file')
                ])
                ->log('updated document');

            return redirect()->route('admin.documents.show', $document)->with('success', 'Dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Document $document)
    {
        try {
            // Hapus file dari storage
            if ($document->file_path && Storage::exists('public/' . $document->file_path)) {
                Storage::delete('public/' . $document->file_path);
            }

            // Hapus file preview jika ada
            $previewPath = 'previews/' . pathinfo($document->file_path, PATHINFO_FILENAME) . '.pdf';
            if (Storage::exists('public/' . $previewPath)) {
                Storage::delete('public/' . $previewPath);
            }

            // Simpan nama untuk log
            $documentName = $document->name;
            
            // Hapus dokumen
            $document->delete();

            // Log aktivitas
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['name' => $documentName])
                ->log('deleted document');

            // Menggunakan respons Inertia untuk menavigasi ulang ke halaman indeks
            return Inertia::location(route('admin.documents.index', ['success' => 'Dokumen berhasil dihapus.']));
        } catch (\Exception $e) {
            return redirect()->route('admin.documents.index')
                ->with('error', 'Terjadi kesalahan saat menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            // Set batas waktu eksekusi yang lebih lama untuk export besar
            set_time_limit(300);
            
            // Query berdasarkan filter pencarian jika ada
            $query = Document::query()
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->with('user');
                
            // Untuk export yang lebih efisien, batasi jumlah records
            $count = $query->count();
            if ($count > 10000) {
                return back()->with('error', 'Jumlah data terlalu banyak (lebih dari 10.000 records). Silakan gunakan filter pencarian untuk mempersempit data export.');
            }
            
            // Ambil data
            $documents = $query->get();
            
            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Format header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EFEFEF'],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];
            
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
            
            // Set header kolom
            $sheet->setCellValue('A1', 'Nama Dokumen');
            $sheet->setCellValue('B1', 'Deskripsi');
            $sheet->setCellValue('C1', 'Pengirim');
            $sheet->setCellValue('D1', 'WhatsApp');
            $sheet->setCellValue('E1', 'Asal Kota');
            $sheet->setCellValue('F1', 'Status');
            
            // Sesuaikan lebar kolom
            $sheet->getColumnDimension('A')->setWidth(40);
            $sheet->getColumnDimension('B')->setWidth(50);
            $sheet->getColumnDimension('C')->setWidth(25);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(15);
            
            // Isi data
            $row = 2;
            foreach ($documents as $document) {
                // Ambil data metadata dengan safe navigation
                $pengirim = $document->metadata['pengirim'] ?? '';
                $whatsapp = $document->metadata['whatsapp'] ?? '';
                $city = $document->metadata['city'] ?? '';
                
                // Jika pengirim tidak ada di metadata, coba ambil dari format lain
                if (empty($pengirim)) {
                    if (!empty($document->metadata['name'])) {
                        $pengirim = $document->metadata['name'];
                    } elseif (!empty($document->description) && strpos($document->description, 'pengunjung:') !== false) {
                        $pengirim = trim(str_replace('Dari pengunjung:', '', $document->description));
                    } elseif (!empty($document->user) && $document->user->name) {
                        $pengirim = $document->user->name;
                    }
                }
                
                // Set nilai sel
                $sheet->setCellValue('A' . $row, $document->name);
                $sheet->setCellValue('B' . $row, $document->description);
                $sheet->setCellValue('C' . $row, $pengirim);
                $sheet->setCellValue('D' . $row, $whatsapp);
                $sheet->setCellValue('E' . $row, $city);
                $sheet->setCellValue('F' . $row, $document->status);
                
                $row++;
            }
            
            // Simpan sebagai file CSV untuk performa yang lebih baik
            $filename = 'dokumen_export_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Buat direktori temporary jika belum ada
            $tempDir = storage_path('app/public/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            $filePath = $tempDir . '/' . $filename;
            
            // Gunakan writer CSV
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);
            $writer->save($filePath);
            
            // Log aktivitas
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['count' => count($documents)])
                ->log('exported documents to CSV');
            
            // Return file untuk download
            return Response::download($filePath, $filename, [
                'Content-Type' => 'text/csv',
            ])->deleteFileAfterSend(true);
        } 
        catch (\Exception $e) {
            Log::error('Error exporting documents: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }
    
    public function template()
    {
        try {
            // Buat spreadsheet dasar
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Sesuaikan lebar kolom
            $sheet->getColumnDimension('A')->setWidth(25);
            $sheet->getColumnDimension('B')->setWidth(35);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(10);
            
            // Format header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EFEFEF'],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];
            
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
            
            // Tambahkan header
            $sheet->setCellValue('A1', 'Nama Dokumen');
            $sheet->setCellValue('B1', 'Deskripsi');
            $sheet->setCellValue('C1', 'Pengirim');
            $sheet->setCellValue('D1', 'WhatsApp');
            $sheet->setCellValue('E1', 'Asal Kota');
            $sheet->setCellValue('F1', 'Status');
            
            // Tambahkan contoh data
            $sheet->setCellValue('A2', 'Contoh Dokumen 1');
            $sheet->setCellValue('B2', 'Ini adalah contoh dokumen pertama');
            $sheet->setCellValue('C2', 'John Doe');
            $sheet->setCellValue('D2', '08123456789');
            $sheet->setCellValue('E2', 'Jakarta');
            $sheet->setCellValue('F2', 'pending');
            
            $sheet->setCellValue('A3', 'Contoh Dokumen 2');
            $sheet->setCellValue('B3', 'Ini adalah contoh dokumen kedua');
            $sheet->setCellValue('C3', 'Jane Smith');
            $sheet->setCellValue('D3', '08987654321');
            $sheet->setCellValue('E3', 'Surabaya');
            $sheet->setCellValue('F3', 'approved');
            
            // Simpan sebagai CSV untuk performa yang lebih baik
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);

            $fileName = 'template_import_dokumen.csv';
            $tempPath = storage_path('app/public/temp/' . $fileName);
            
            // Pastikan direktori ada
            if (!file_exists(storage_path('app/public/temp'))) {
                mkdir(storage_path('app/public/temp'), 0777, true);
            }
            
            $writer->save($tempPath);
            
            // Log aktivitas
            activity()
                ->causedBy(auth()->user())
                ->log('downloaded csv template');
            
            // Header untuk download
            return Response::download($tempPath, $fileName, [
                'Content-Type' => 'text/csv',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error generating template: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat template: ' . $e->getMessage());
        }
    }
    
    public function import(Request $request)
    {
        try {
            // Validasi - hanya terima file CSV
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:5120', // Max 5MB, menerima file CSV atau TXT
            ]);
            
            $file = $request->file('file');
            $filePath = $file->getRealPath();
            
            // Set batas waktu eksekusi yang lebih lama untuk file besar
            set_time_limit(600); // 10 menit
            
            // Untuk file CSV sangat besar, gunakan streaming parser
            if ($file->getSize() > 1 * 1024 * 1024) { // Jika lebih dari 1MB
                return $this->importLargeCSV($file);
            }
            
            // Gunakan pembacaan CSV yang lebih efisien
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setSheetIndex(0);
            $reader->setReadDataOnly(true); // Hanya baca data, abaikan formatting
            
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, false, true);
            
            // Header ada di baris pertama
            $header = array_shift($rows);
            
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
            
            // Validasi header minimal harus ada kolom nama
            if (!isset($columnMap['name'])) {
                return back()->withErrors(['file' => 'Format file tidak valid. Kolom Nama Dokumen wajib ada.']);
            }
            
            // Gunakan transaksi database untuk efisiensi
            \DB::beginTransaction();
            
            try {
                $imported = 0;
                $errors = [];
                $documentBatch = [];
                
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
                    if (isset($columnMap['pengirim']) && !empty($row[$columnMap['pengirim']])) {
                        $metadata['pengirim'] = trim($row[$columnMap['pengirim']]);
                    }
                    
                    // Tambahkan metadata whatsapp jika ada
                    if (isset($columnMap['whatsapp']) && !empty($row[$columnMap['whatsapp']])) {
                        $metadata['whatsapp'] = trim($row[$columnMap['whatsapp']]);
                    }
                    
                    // Tambahkan metadata kota jika ada
                    if (isset($columnMap['city']) && !empty($row[$columnMap['city']])) {
                        $metadata['city'] = trim($row[$columnMap['city']]);
                    }
                    
                    // Tentukan status (default: pending)
                    $status = 'pending';
                    if (isset($columnMap['status']) && !empty($row[$columnMap['status']])) {
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
                        Document::insert($batch);
                        $batch = [];
                    }
                }
                
                // Insert sisa data yang belum di-batch
                if (!empty($batch)) {
                    Document::insert($batch);
                }
                
                \DB::commit();
                
                // Log aktivitas
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['imported_count' => $imported])
                    ->log('imported documents from CSV');
                
                $message = "Berhasil mengimpor {$imported} dokumen.";
                if (count($errors) > 0) {
                    $message .= " Dengan " . count($errors) . " error.";
                }
                
                return redirect()->back()->with('success', $message);
                
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error importing documents: ' . $e->getMessage());
            return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Import file CSV yang sangat besar dengan metode streaming
     */
    private function importLargeCSV($file)
    {
        try {
            $filePath = $file->getRealPath();
            
            // Buka file untuk dibaca baris per baris
            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception('Tidak dapat membuka file');
            }
            
            // Baca baris header
            $header = fgetcsv($handle, 0, ',', '"');
            if ($header === false) {
                throw new \Exception('File CSV kosong atau rusak');
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
                return back()->withErrors(['file' => 'Format file tidak valid. Kolom Nama Dokumen wajib ada.']);
            }
            
            // Mulai transaksi database
            \DB::beginTransaction();
            
            $imported = 0;
            $errors = [];
            $rowNum = 1;
            $batchSize = 500;
            $batch = [];
            
            // Proses data baris per baris untuk menghemat memori
            while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
                $rowNum++;
                
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Ambil nama dokumen
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
                    Document::insert($batch);
                    $batch = [];
                }
            }
            
            // Insert sisa data yang belum di-batch
            if (!empty($batch)) {
                Document::insert($batch);
            }
            
            // Tutup file
            fclose($handle);
            
            \DB::commit();
            
            // Log aktivitas
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['imported_count' => $imported])
                ->log('imported large CSV file with documents');
            
            $message = "Berhasil mengimpor {$imported} dokumen.";
            if (count($errors) > 0) {
                $message .= " Dengan " . count($errors) . " error.";
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            
            \DB::rollBack();
            
            Log::error('Error importing large CSV: ' . $e->getMessage());
            return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan saat mengimpor file besar: ' . $e->getMessage()]);
        }
    }
} 
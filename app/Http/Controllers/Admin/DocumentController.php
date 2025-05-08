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
            // Ambil dokumen berdasarkan filter pencarian jika ada
            $query = Document::query()
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->with('user');
                
            $documents = $query->latest()->get();
            
            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set judul kolom
            $sheet->setCellValue('A1', 'No.');
            $sheet->setCellValue('B1', 'Nama Dokumen');
            $sheet->setCellValue('C1', 'Nama File');
            $sheet->setCellValue('D1', 'Pengirim');
            $sheet->setCellValue('E1', 'WhatsApp');
            $sheet->setCellValue('F1', 'Asal Kota');
            $sheet->setCellValue('G1', 'Deskripsi');
            $sheet->setCellValue('H1', 'Status');
            $sheet->setCellValue('I1', 'Tanggal Upload');
            $sheet->setCellValue('J1', 'Ukuran File');
            
            // Style header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E6E6FA',
                    ],
                ],
            ];
            
            $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
            
            // Isi data
            $row = 2;
            foreach ($documents as $index => $document) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $document->name);
                $sheet->setCellValue('C' . $row, $document->file_name);
                
                // Pengirim
                $pengirim = $document->user ? $document->user->name : 'Pengunjung';
                if (isset($document->metadata['pengirim'])) {
                    $pengirim = $document->metadata['pengirim'];
                } elseif (isset($document->metadata['name'])) {
                    $pengirim = $document->metadata['name'];
                } elseif ($document->description && str_contains($document->description, 'Dari pengunjung:')) {
                    $pengirim = trim(str_replace('Dari pengunjung:', '', $document->description));
                }
                
                $sheet->setCellValue('D' . $row, $pengirim);
                
                // WhatsApp dan Kota
                $whatsapp = isset($document->metadata['whatsapp']) ? $document->metadata['whatsapp'] : '-';
                $city = isset($document->metadata['city']) ? $document->metadata['city'] : '-';
                
                $sheet->setCellValue('E' . $row, $whatsapp);
                $sheet->setCellValue('F' . $row, $city);
                $sheet->setCellValue('G' . $row, $document->description);
                
                // Status
                $statusLabels = [
                    'pending' => 'Menunggu',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak'
                ];
                $status = $statusLabels[$document->status] ?? $document->status;
                
                $sheet->setCellValue('H' . $row, $status);
                $sheet->setCellValue('I' . $row, Carbon::parse($document->uploaded_at)->format('d-m-Y H:i:s'));
                
                // Format ukuran file
                $fileSize = $document->file_size;
                $sizeLabel = $fileSize . ' Bytes';
                
                if ($fileSize >= 1024) {
                    $fileSize = round($fileSize / 1024, 2);
                    $sizeLabel = $fileSize . ' KB';
                }
                
                if ($fileSize >= 1024) {
                    $fileSize = round($fileSize / 1024, 2);
                    $sizeLabel = $fileSize . ' MB';
                }
                
                $sheet->setCellValue('J' . $row, $sizeLabel);
                
                $row++;
            }
            
            // Auto-size kolom
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk semua data
            $lastRow = $row - 1;
            if ($lastRow >= 2) {
                $sheet->getStyle('A2:J' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            }
            
            // Simpan ke file excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'dokumen_' . date('Ymd_His') . '.xlsx';
            $tempPath = storage_path('app/public/temp/' . $fileName);
            
            // Pastikan direktori ada
            if (!file_exists(storage_path('app/public/temp'))) {
                mkdir(storage_path('app/public/temp'), 0777, true);
            }
            
            $writer->save($tempPath);
            
            // Log aktivitas
            activity()
                ->causedBy(auth()->user())
                ->log('exported documents');
            
            // Header untuk download
            return Response::download($tempPath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error exporting documents: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }
    
    public function template()
    {
        try {
            // Buat spreadsheet untuk template
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set judul kolom
            $sheet->setCellValue('A1', 'Nama Dokumen');
            $sheet->setCellValue('B1', 'Deskripsi');
            $sheet->setCellValue('C1', 'Pengirim');
            $sheet->setCellValue('D1', 'WhatsApp');
            $sheet->setCellValue('E1', 'Asal Kota');
            $sheet->setCellValue('F1', 'Status');
            
            // Style header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E6E6FA',
                    ],
                ],
            ];
            
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
            
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
            
            // Auto-size kolom
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk semua data
            $sheet->getStyle('A2:F3')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
            
            // Simpan ke file excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'template_import_dokumen.xlsx';
            $tempPath = storage_path('app/public/temp/' . $fileName);
            
            // Pastikan direktori ada
            if (!file_exists(storage_path('app/public/temp'))) {
                mkdir(storage_path('app/public/temp'), 0777, true);
            }
            
            $writer->save($tempPath);
            
            // Header untuk download
            return Response::download($tempPath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error generating template: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat template: ' . $e->getMessage());
        }
    }
    
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx,xls|max:5120', // Max 5MB
            ]);
            
            $file = $request->file('file');
            $filePath = $file->getRealPath();
            
            // Baca file Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Header ada di baris pertama
            $header = array_shift($rows);
            
            // Mapping kolom dengan header
            $columnMap = [];
            foreach ($header as $index => $columnName) {
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
                return response()->json(['success' => false, 'error' => 'Format file tidak valid. Kolom Nama Dokumen wajib ada.']);
            }
            
            $imported = 0;
            $errors = [];
            
            foreach ($rows as $rowIndex => $row) {
                $rowNum = $rowIndex + 2; // +2 karena baris pertama adalah header dan array dimulai dari 0
                
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }
                
                $name = $row[$columnMap['name']] ?? null;
                
                // Validasi nama tidak boleh kosong
                if (empty($name)) {
                    $errors[] = "Baris {$rowNum}: Nama dokumen tidak boleh kosong.";
                    continue;
                }
                
                // Siapkan data
                $documentData = [
                    'name' => $name,
                    'description' => $row[$columnMap['description']] ?? null,
                    'user_id' => auth()->id(),
                    'uploaded_at' => Carbon::now(),
                    'status' => 'pending',
                    'metadata' => [],
                ];
                
                // Status jika ada
                if (isset($columnMap['status']) && !empty($row[$columnMap['status']])) {
                    $status = strtolower(trim($row[$columnMap['status']]));
                    if (in_array($status, ['pending', 'approved', 'rejected'])) {
                        $documentData['status'] = $status;
                    }
                }
                
                // Tambahkan metadata
                if (isset($columnMap['pengirim']) && !empty($row[$columnMap['pengirim']])) {
                    $documentData['metadata']['pengirim'] = $row[$columnMap['pengirim']];
                }
                
                if (isset($columnMap['whatsapp']) && !empty($row[$columnMap['whatsapp']])) {
                    $documentData['metadata']['whatsapp'] = $row[$columnMap['whatsapp']];
                }
                
                if (isset($columnMap['city']) && !empty($row[$columnMap['city']])) {
                    $documentData['metadata']['city'] = $row[$columnMap['city']];
                }
                
                // Buat dokumen tanpa file (file harus diupload manual kemudian)
                Document::create($documentData);
                $imported++;
            }
            
            // Log aktivitas
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['imported_count' => $imported])
                ->log('imported documents');
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$imported} dokumen." . (count($errors) > 0 ? " Dengan " . count($errors) . " error." : ""),
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing documents: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
} 
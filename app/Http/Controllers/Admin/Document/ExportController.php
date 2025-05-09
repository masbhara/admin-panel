<?php

namespace App\Http\Controllers\Admin\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Models\Document;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportController extends Controller
{
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
} 
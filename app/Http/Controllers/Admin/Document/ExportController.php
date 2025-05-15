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
use Carbon\Carbon;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        try {
            // Ambil data dokumen dengan filter jika ada
            $documents = Document::query()
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->with('user')
                ->get();

            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set header kolom
            $sheet->setCellValue('A1', 'Nama File Asli');
            $sheet->setCellValue('B1', 'Pengirim');
            $sheet->setCellValue('C1', 'WhatsApp Pengirim');
            $sheet->setCellValue('D1', 'Asal Kota');
            $sheet->setCellValue('E1', 'Deskripsi');
            $sheet->setCellValue('F1', 'Tipe File');
            $sheet->setCellValue('G1', 'Ukuran File');
            $sheet->setCellValue('H1', 'Status');
            $sheet->setCellValue('I1', 'Tanggal Upload');
            $sheet->setCellValue('J1', 'Diproses Oleh');

            // Style untuk header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E2E8F0',
                    ],
                ],
            ];
            $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

            // Set data
            $row = 2;
            foreach ($documents as $document) {
                // Format file size ke KB/MB
                $fileSize = $document->file_size;
                if ($fileSize >= 1024 * 1024) {
                    $formattedSize = round($fileSize / (1024 * 1024), 2) . ' MB';
                } else {
                    $formattedSize = round($fileSize / 1024, 2) . ' KB';
                }

                // Set nilai sel
                $sheet->setCellValue('A' . $row, $document->file_name ?: $document->name);
                $sheet->setCellValue('B' . $row, $document->metadata['pengirim'] ?? '');
                $sheet->setCellValue('C' . $row, $document->metadata['whatsapp'] ?? '');
                $sheet->setCellValue('D' . $row, $document->metadata['city'] ?? '');
                $sheet->setCellValue('E' . $row, $document->description);
                $sheet->setCellValue('F' . $row, $document->file_type);
                $sheet->setCellValue('G' . $row, $formattedSize);
                $sheet->setCellValue('H' . $row, $document->status);
                $sheet->setCellValue('I' . $row, $document->uploaded_at ? Carbon::parse($document->uploaded_at)->format('d M Y') : '');
                $sheet->setCellValue('J' . $row, $document->user ? $document->user->name : 'Sistem');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Simpan sebagai file CSV
            $filename = 'dokumen_export_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Buat direktori temporary jika belum ada
            $tempDir = storage_path('app/public/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            $filePath = $tempDir . '/' . $filename;
            
            // Gunakan writer CSV dengan pengaturan yang sesuai
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
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header kolom
            $sheet->setCellValue('A1', 'Nama File Asli');
            $sheet->setCellValue('B1', 'Pengirim');
            $sheet->setCellValue('C1', 'WhatsApp Pengirim');
            $sheet->setCellValue('D1', 'Asal Kota');
            $sheet->setCellValue('E1', 'Deskripsi');
            $sheet->setCellValue('F1', 'Tipe File');
            $sheet->setCellValue('G1', 'Ukuran File');
            $sheet->setCellValue('H1', 'Status');
            $sheet->setCellValue('I1', 'Tanggal Upload');
            $sheet->setCellValue('J1', 'Diproses Oleh');
            
            // Style untuk header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E2E8F0',
                    ],
                ],
            ];
            $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
            
            // Contoh data
            $sheet->setCellValue('A2', 'Contoh-Dokumen-1.docx');
            $sheet->setCellValue('B2', 'John Doe');
            $sheet->setCellValue('C2', '081234567890');
            $sheet->setCellValue('D2', 'Jakarta');
            $sheet->setCellValue('E2', 'Ini adalah contoh dokumen pertama');
            $sheet->setCellValue('F2', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $sheet->setCellValue('G2', '15.5 KB');
            $sheet->setCellValue('H2', 'pending');
            $sheet->setCellValue('I2', date('d M Y'));
            $sheet->setCellValue('J2', 'Admin');

            // Auto-size columns
            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Simpan sebagai CSV
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
            
            return Response::download($tempPath, $fileName, [
                'Content-Type' => 'text/csv',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error generating template: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat template: ' . $e->getMessage());
        }
    }
} 
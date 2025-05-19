<?php

namespace App\Http\Controllers\Admin\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    /**
     * Export dokumen ke file Excel
     */
    public function export(Request $request)
    {
        try {
            $documentFormId = $request->input('document_form_id');
            
            // Ambil dokumen berdasarkan document_form_id
            $query = Document::query();
            
            if ($documentFormId) {
                $query->where('document_form_id', $documentFormId);
            }
            
            // Filter berdasarkan status jika ada
            if ($request->has('status') && $request->input('status') !== 'all') {
                $query->where('status', $request->input('status'));
            }
            
            // Filter berdasarkan tanggal jika ada
            if ($request->has('start_date')) {
                $query->where('created_at', '>=', $request->input('start_date'));
            }
            
            if ($request->has('end_date')) {
                $query->where('created_at', '<=', $request->input('end_date') . ' 23:59:59');
            }
            
            // Load dokumen dengan relasi yang dibutuhkan
            $documents = $query->with(['documentForm', 'user'])->get();
            
            // Buat file Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Nama');
            $sheet->setCellValue('C1', 'WhatsApp');
            $sheet->setCellValue('D1', 'Kota');
            $sheet->setCellValue('E1', 'Status');
            $sheet->setCellValue('F1', 'Tanggal Upload');
            $sheet->setCellValue('G1', 'Nama File');
            $sheet->setCellValue('H1', 'Form');
            $sheet->setCellValue('I1', 'Catatan');
            
            // Style header
            $sheet->getStyle('A1:I1')->getFont()->setBold(true);
            
            // Isi data
            $row = 2;
            foreach ($documents as $document) {
                $sheet->setCellValue('A' . $row, $document->id);
                $sheet->setCellValue('B' . $row, $document->name ?? ($document->metadata['name'] ?? '-'));
                $sheet->setCellValue('C' . $row, $document->metadata['whatsapp'] ?? '-');
                $sheet->setCellValue('D' . $row, $document->metadata['city'] ?? '-');
                $sheet->setCellValue('E' . $row, $document->status ?? 'pending');
                $sheet->setCellValue('F' . $row, $document->uploaded_at ?? $document->created_at);
                $sheet->setCellValue('G' . $row, $document->file_name ?? '-');
                $sheet->setCellValue('H' . $row, $document->documentForm->title ?? '-');
                $sheet->setCellValue('I' . $row, $document->notes ?? '-');
                
                $row++;
            }
            
            // Auto size columns
            foreach(range('A','I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $filename = 'dokumen-export-' . date('Y-m-d-His') . '.xlsx';
            $filepath = storage_path('app/public/exports/' . $filename);
            
            // Make sure the directory exists
            if (!file_exists(storage_path('app/public/exports'))) {
                mkdir(storage_path('app/public/exports'), 0755, true);
            }
            
            // Save file
            $writer->save($filepath);
            
            // Return file download
            return response()->download($filepath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error exporting documents: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'error' => 'Gagal mengekspor dokumen: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Download template untuk import dokumen
     */
    public function downloadTemplate()
    {
        try {
            // Buat file Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $sheet->setCellValue('A1', 'Nama');
            $sheet->setCellValue('B1', 'WhatsApp');
            $sheet->setCellValue('C1', 'Kota');
            $sheet->setCellValue('D1', 'Informasi Tambahan 1');
            $sheet->setCellValue('E1', 'Informasi Tambahan 2');
            
            // Style header
            $sheet->getStyle('A1:E1')->getFont()->setBold(true);
            
            // Contoh data
            $sheet->setCellValue('A2', 'John Doe');
            $sheet->setCellValue('B2', '081234567890');
            $sheet->setCellValue('C2', 'Jakarta');
            $sheet->setCellValue('D2', 'Informasi 1');
            $sheet->setCellValue('E2', 'Informasi 2');
            
            $sheet->setCellValue('A3', 'Jane Smith');
            $sheet->setCellValue('B3', '085678901234');
            $sheet->setCellValue('C3', 'Surabaya');
            $sheet->setCellValue('D3', 'Informasi 1');
            $sheet->setCellValue('E3', 'Informasi 2');
            
            // Add note
            $sheet->setCellValue('A5', 'Catatan:');
            $sheet->setCellValue('A6', '1. Kolom Nama wajib diisi.');
            $sheet->setCellValue('A7', '2. Format WhatsApp: 08XXXXXXXXXX (tanpa tanda + atau spasi).');
            $sheet->setCellValue('A8', '3. Kolom Informasi Tambahan bersifat opsional.');
            
            // Auto size columns
            foreach(range('A','E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $filename = 'template-import-dokumen.xlsx';
            $filepath = storage_path('app/public/' . $filename);
            
            // Save file
            $writer->save($filepath);
            
            // Return file download
            return response()->download($filepath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error downloading template: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'error' => 'Gagal mengunduh template: ' . $e->getMessage()
            ]);
        }
    }
} 
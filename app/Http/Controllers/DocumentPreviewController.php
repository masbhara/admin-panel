<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DocumentPreviewController extends Controller
{
    /**
     * Menampilkan preview dokumen
     *
     * @param  Document  $document
     * @return \Illuminate\Http\Response
     */
    public function preview(Document $document)
    {
        if (!$document->file_path || !Storage::exists($document->file_path)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        $mimeType = Storage::mimeType($document->file_path);
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $filename = pathinfo($document->file_path, PATHINFO_FILENAME);
        $previewPath = 'public/previews/' . $filename . '.pdf';
        
        // Cek apakah preview sudah ada
        if (Storage::exists($previewPath)) {
            return response()->json([
                'previewUrl' => Storage::url('previews/' . $filename . '.pdf'),
                'originalUrl' => Storage::url($document->file_path),
                'type' => 'pdf'
            ]);
        }
        
        // Tipe file yang bisa di-preview langsung
        if (in_array($mimeType, ['application/pdf'])) {
            return response()->json([
                'previewUrl' => Storage::url($document->file_path),
                'originalUrl' => Storage::url($document->file_path),
                'type' => 'pdf'
            ]);
        }
        
        // Format yang didukung untuk konversi
        $supportedFormats = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // docx
            'application/msword', // doc
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
            'application/vnd.ms-excel', // xls
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // pptx
            'application/vnd.ms-powerpoint', // ppt
            'text/plain', // txt
            'text/csv', // csv
            'application/rtf', // rtf
        ];
        
        if (in_array($mimeType, $supportedFormats)) {
            // Coba konversi dengan LibreOffice
            $result = $this->convertToPdf($document->file_path, $previewPath);
            
            if ($result) {
                return response()->json([
                    'previewUrl' => Storage::url('previews/' . $filename . '.pdf'),
                    'originalUrl' => Storage::url($document->file_path),
                    'type' => 'pdf'
                ]);
            }
        }
        
        // Fallback ke external viewer
        return $this->externalViewer($document);
    }
    
    /**
     * Konversi dokumen ke PDF dengan LibreOffice
     *
     * @param  string  $sourcePath
     * @param  string  $targetPath
     * @return bool
     */
    private function convertToPdf($sourcePath, $targetPath)
    {
        try {
            $sourceFullPath = Storage::path($sourcePath);
            $targetDir = Storage::path('public/previews');
            $targetFullPath = Storage::path($targetPath);
            
            // Pastikan direktori target ada
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Jalankan LibreOffice untuk konversi
            $process = new Process([
                'libreoffice',
                '--headless',
                '--convert-to',
                'pdf',
                '--outdir',
                $targetDir,
                $sourceFullPath
            ]);
            
            $process->setTimeout(60); // Timeout 60 detik
            $process->run();
            
            if (!$process->isSuccessful()) {
                Log::error('LibreOffice conversion failed: ' . $process->getErrorOutput());
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('PDF conversion error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gunakan viewer eksternal untuk preview
     *
     * @param  Document  $document
     * @return \Illuminate\Http\Response
     */
    private function externalViewer(Document $document)
    {
        $publicUrl = config('app.url') . Storage::url($document->file_path);
        $encodedUrl = urlencode($publicUrl);
        
        // Google Docs Viewer
        $googleViewerUrl = 'https://docs.google.com/viewer?url=' . $encodedUrl . '&embedded=true';
        
        // Office Online Viewer
        $officeViewerUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' . $encodedUrl;
        
        return response()->json([
            'previewUrl' => $googleViewerUrl,
            'officeViewerUrl' => $officeViewerUrl,
            'originalUrl' => Storage::url($document->file_path),
            'type' => 'external'
        ]);
    }
    
    /**
     * Download original dokumen
     *
     * @param  Document  $document
     * @return \Illuminate\Http\Response
     */
    public function download(Document $document)
    {
        try {
            // Ambil file yang ada di folder documents di storage
            $file = scandir(storage_path('app/public/documents'))[2] ?? null;
            
            if ($file) {
                $filePath = storage_path('app/public/documents/' . $file);
                $fileName = $document->name . '.' . pathinfo($file, PATHINFO_EXTENSION);
                
                Log::info('Download file langsung: ' . $filePath);
                
                // Log aktivitas
                if (auth()->check()) {
                    activity()
                        ->performedOn($document)
                        ->causedBy(auth()->user())
                        ->log('downloaded document');
                }
                
                // Kirim file secara langsung
                return response()->download($filePath, $fileName);
            }
            
            return back()->with('error', 'File tidak ditemukan di server');
        } catch (\Exception $e) {
            Log::error('Document download error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh dokumen: ' . $e->getMessage());
        }
    }
} 
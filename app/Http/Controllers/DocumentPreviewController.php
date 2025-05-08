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
        try {
            // Tambahkan debugging file path
            Log::info('Preview request for document: ' . $document->id . ', Path: ' . $document->file_path);
            
            // Cek apakah file path kosong
            if (empty($document->file_path)) {
                Log::error('Document has empty file_path: ' . $document->id);
                return response()->json([
                    'success' => false,
                    'error' => 'File path tidak valid'
                ], 404);
            }
            
            // Cek keberadaan file di storage dengan berbagai kemungkinan path
            $filePath = $this->resolveFilePath($document->file_path);
            
            if (!$filePath) {
                Log::error('File does not exist: ' . $document->file_path);
                return response()->json([
                    'success' => false,
                    'error' => 'File tidak ditemukan di server'
                ], 404);
            }

            // Update file path jika berbeda
            if ($filePath !== $document->file_path) {
                $document->file_path = $filePath;
            }

            $mimeType = Storage::mimeType($filePath);
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            
            Log::info('Document mime type: ' . $mimeType . ', Extension: ' . $extension);
            
            // Tipe file yang bisa di-preview langsung (PDF)
            if (strtolower($extension) === 'pdf' || $mimeType === 'application/pdf') {
                Log::info('Using direct PDF preview');
                return response()->json([
                    'success' => true,
                    'previewUrl' => Storage::url($filePath),
                    'type' => 'pdf'
                ]);
            }
            
            // Untuk file selain PDF, gunakan external viewer (Google/Microsoft)
            return $this->externalViewer($document, $filePath);
        } catch (\Exception $e) {
            Log::error('Preview error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memuat preview: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Resolve file path dengan berbagai kemungkinan
     * 
     * @param string $originalPath
     * @return string|null
     */
    private function resolveFilePath($originalPath)
    {
        // Daftar kemungkinan path
        $possiblePaths = [
            $originalPath,
            'public/' . $originalPath,
            ltrim($originalPath, 'public/'),
            str_replace('//', '/', $originalPath)
        ];
        
        foreach ($possiblePaths as $path) {
            if (Storage::exists($path)) {
                Log::info('Found valid file path: ' . $path);
                return $path;
            }
        }
        
        // Jika file storage link tidak bekerja, coba cek secara langsung di disk
        $basePath = storage_path('app/public');
        $strippedPath = ltrim($originalPath, 'public/');
        
        if (file_exists($basePath . '/' . $strippedPath)) {
            $publicPath = 'public/' . $strippedPath;
            Log::info('Found file directly in disk: ' . $publicPath);
            return $publicPath;
        }
        
        return null;
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
     * @param  string  $filePath
     * @return \Illuminate\Http\Response
     */
    private function externalViewer(Document $document, $filePath = null)
    {
        $filePath = $filePath ?? $document->file_path;
        $baseUrl = config('app.url');
        $fileUrl = Storage::url($filePath);
        $publicUrl = $baseUrl . $fileUrl;
        $encodedUrl = urlencode($publicUrl);
        
        // Cek apakah URL adalah localhost
        $isLocalEnvironment = str_contains($baseUrl, 'localhost') || 
                            str_contains($baseUrl, '127.0.0.1') || 
                            str_contains($baseUrl, '::1') || 
                            str_contains($baseUrl, '.test') ||
                            str_contains($baseUrl, '.local');
        
        // Log informasi debugging
        Log::info('External viewer URL info:', [
            'baseUrl' => $baseUrl,
            'fileUrl' => $fileUrl,
            'publicUrl' => $publicUrl,
            'isLocal' => $isLocalEnvironment
        ]);
        
        // Google Docs Viewer
        $googleViewerUrl = 'https://docs.google.com/viewer?url=' . $encodedUrl . '&embedded=true';
        
        // Office Online Viewer
        $officeViewerUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' . $encodedUrl;
        
        $responseData = [
            'success' => true,
            'previewUrl' => $googleViewerUrl,
            'googleViewerUrl' => $googleViewerUrl,
            'officeViewerUrl' => $officeViewerUrl,
            'type' => 'external',
            'originalUrl' => $publicUrl
        ];
        
        // Jika di lingkungan lokal, tambahkan pesan warning
        if ($isLocalEnvironment) {
            $responseData['localEnvironment'] = true;
            $responseData['warning'] = 'Preview dokumen menggunakan Google/Microsoft pada lingkungan lokal (localhost) tidak akan berfungsi dengan sempurna karena file tidak dapat diakses oleh layanan eksternal. Silakan gunakan server publik untuk preview yang bekerja penuh.';
        }
        
        return response()->json($responseData);
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
            Log::info('Download request for document: ' . $document->id . ', Path: ' . $document->file_path);
            
            // Cek apakah file path kosong
            if (empty($document->file_path)) {
                Log::error('Document has empty file_path: ' . $document->id);
                return back()->with('error', 'File path tidak valid');
            }
            
            // Cek keberadaan file di storage dengan berbagai kemungkinan path
            $filePath = $this->resolveFilePath($document->file_path);
            
            if (!$filePath) {
                Log::error('File does not exist: ' . $document->file_path);
                return back()->with('error', 'File tidak ditemukan di server');
            }
            
            $fullPath = storage_path('app/' . $filePath);
            
            // Gunakan nama file asli jika ada, jika tidak gunakan nama dokumen
            $fileName = $document->file_name ?? $document->name;
            
            // Tambahkan ekstensi jika tidak ada
            if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $fileName = $fileName . '.' . $extension;
            }
            
            Log::info('Serving file from: ' . $fullPath . ' as ' . $fileName);
            
            // Log aktivitas jika user login
            if (auth()->check()) {
                activity()
                    ->performedOn($document)
                    ->causedBy(auth()->user())
                    ->log('downloaded document');
            }
            
            // Tambahkan header yang tepat untuk download
            return response()->download($fullPath, $fileName);
        } catch (\Exception $e) {
            Log::error('Document download error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh dokumen: ' . $e->getMessage());
        }
    }
} 
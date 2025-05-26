<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentDownloadController extends Controller
{
    /**
     * Download dokumen
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
            
            // Verifikasi file ada secara fisik
            if (!file_exists($fullPath)) {
                Log::error('File tidak ditemukan di disk: ' . $fullPath);
                
                // Coba cari di lokasi alternatif
                $alternativePaths = [
                    storage_path('app/public/' . basename($document->file_path)),
                    public_path('storage/' . basename($document->file_path)),
                    public_path('storage/documents/' . basename($document->file_path)),
                ];
                
                foreach ($alternativePaths as $path) {
                    if (file_exists($path)) {
                        Log::info('File ditemukan di lokasi alternatif: ' . $path);
                        $fullPath = $path;
                        break;
                    }
                }
                
                if (!file_exists($fullPath)) {
                    return back()->with('error', 'File tidak ditemukan di server');
                }
            }
            
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
            'public/' . ltrim($originalPath, 'public/'),
            ltrim($originalPath, 'public/'),
            str_replace('//', '/', $originalPath),
            'public/documents/' . basename($originalPath),
            'documents/' . basename($originalPath)
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
        
        // Coba cek di direktori dokumen
        $documentPath = $basePath . '/documents/' . basename($originalPath);
        if (file_exists($documentPath)) {
            $publicPath = 'public/documents/' . basename($originalPath);
            Log::info('Found file in documents directory: ' . $publicPath);
            return $publicPath;
        }
        
        return null;
    }
} 
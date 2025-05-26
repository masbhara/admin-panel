<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ScreenshotController extends Controller
{
    /**
     * Download screenshot
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, $documentId)
    {
        try {
            $document = Document::findOrFail($documentId);
            
            if (!isset($document->metadata['screenshot_path'])) {
                Log::error('Screenshot path tidak ditemukan di metadata dokumen', [
                    'document_id' => $documentId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Screenshot tidak ditemukan'
                ], 404);
            }
            
            $screenshotPath = $document->metadata['screenshot_path'];
            
            Log::info('Download screenshot request', [
                'document_id' => $documentId,
                'path' => $screenshotPath
            ]);
            
            // Coba berbagai kemungkinan path
            $possiblePaths = [
                $screenshotPath,
                'public/' . ltrim($screenshotPath, 'public/'),
                ltrim($screenshotPath, 'public/'),
                str_replace('//', '/', $screenshotPath)
            ];
            
            $validPath = null;
            foreach ($possiblePaths as $path) {
                if (Storage::exists($path)) {
                    $validPath = $path;
                    Log::info('Valid screenshot path found', ['path' => $path]);
                    break;
                }
            }
            
            if (!$validPath) {
                Log::error('Screenshot tidak ditemukan di storage', [
                    'tried_paths' => $possiblePaths
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Screenshot tidak ditemukan di server'
                ], 404);
            }
            
            $filename = basename($validPath);
            $fullPath = storage_path('app/' . $validPath);
            
            Log::info('Serving screenshot from', [
                'path' => $fullPath,
                'filename' => $filename
            ]);
            
            return response()->download($fullPath, $filename);
        } catch (\Exception $e) {
            Log::error('Error downloading screenshot', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * View screenshot
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $documentId)
    {
        try {
            $document = Document::findOrFail($documentId);
            
            if (!isset($document->metadata['screenshot_path'])) {
                Log::error('Screenshot path tidak ditemukan di metadata dokumen', [
                    'document_id' => $documentId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Screenshot tidak ditemukan'
                ], 404);
            }
            
            $screenshotPath = $document->metadata['screenshot_path'];
            
            Log::info('View screenshot request', [
                'document_id' => $documentId,
                'path' => $screenshotPath
            ]);
            
            // Coba berbagai kemungkinan path
            $possiblePaths = [
                $screenshotPath,
                'public/' . ltrim($screenshotPath, 'public/'),
                ltrim($screenshotPath, 'public/'),
                str_replace('//', '/', $screenshotPath)
            ];
            
            $validPath = null;
            foreach ($possiblePaths as $path) {
                if (Storage::exists($path)) {
                    $validPath = $path;
                    Log::info('Valid screenshot path found', ['path' => $path]);
                    break;
                }
            }
            
            if (!$validPath) {
                Log::error('Screenshot tidak ditemukan di storage', [
                    'tried_paths' => $possiblePaths
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Screenshot tidak ditemukan di server'
                ], 404);
            }
            
            $mimeType = Storage::mimeType($validPath);
            $content = Storage::get($validPath);
            
            Log::info('Serving screenshot content', [
                'path' => $validPath,
                'mime' => $mimeType
            ]);
            
            return response($content)->header('Content-Type', $mimeType);
        } catch (\Exception $e) {
            Log::error('Error viewing screenshot', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 
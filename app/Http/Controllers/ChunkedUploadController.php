<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsTransactionsImport;

class ChunkedUploadController extends Controller
{
    const CHUNK_UPLOAD_DIR = 'chunk_uploads';
    const CHUNK_TEMP_DIR = 'chunk_temp';

    /**
     * Handle chunk upload
     */
    public function uploadChunk(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file',
                'chunkIndex' => 'required|integer|min:0',
                'totalChunks' => 'required|integer|min:1',
                'sessionId' => 'required|string',
                'fileName' => 'required|string'
            ]);

            $chunk = $request->file('file');
            $sessionId = $request->input('sessionId');
            $chunkIndex = $request->input('chunkIndex');
            $totalChunks = $request->input('totalChunks');
            $originalFileName = $request->input('fileName');

            // Create session directory
            $sessionDir = storage_path('app/' . self::CHUNK_TEMP_DIR . '/' . $sessionId);
            if (!is_dir($sessionDir)) {
                mkdir($sessionDir, 0755, true);
            }

            // Store chunk
            $chunkName = 'chunk_' . str_pad($chunkIndex, 6, '0', STR_PAD_LEFT);
            $chunkPath = $sessionDir . '/' . $chunkName;
            
            if (!move_uploaded_file($chunk->getRealPath(), $chunkPath)) {
                throw new \Exception('Failed to save chunk ' . $chunkIndex);
            }

            // Check if all chunks are uploaded
            $uploadedChunks = count(glob($sessionDir . '/chunk_*'));
            
            Log::info('Chunk uploaded', [
                'sessionId' => $sessionId,
                'chunkIndex' => $chunkIndex,
                'totalChunks' => $totalChunks,
                'uploadedChunks' => $uploadedChunks,
                'fileName' => $originalFileName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chunk uploaded successfully',
                'chunkIndex' => $chunkIndex,
                'uploadedChunks' => $uploadedChunks
            ]);

        } catch (\Exception $e) {
            Log::error('Chunk upload error', [
                'error' => $e->getMessage(),
                'sessionId' => $request->input('sessionId') ?? 'unknown',
                'chunkIndex' => $request->input('chunkIndex') ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Finalize upload and process file
     */
    public function finalizeUpload(Request $request)
    {
        try {
            $request->validate([
                'sessionId' => 'required|string',
                'fileName' => 'required|string'
            ]);

            $sessionId = $request->input('sessionId');
            $fileName = $request->input('fileName');
            $sessionDir = storage_path('app/' . self::CHUNK_TEMP_DIR . '/' . $sessionId);

            if (!is_dir($sessionDir)) {
                throw new \Exception('Session directory not found');
            }

            // Get all chunks and verify
            $chunks = glob($sessionDir . '/chunk_*');
            if (empty($chunks)) {
                throw new \Exception('No chunks found for this session');
            }

            // Sort chunks by index
            usort($chunks, function($a, $b) {
                $aNum = (int)str_replace('chunk_', '', basename($a));
                $bNum = (int)str_replace('chunk_', '', basename($b));
                return $aNum <=> $bNum;
            });

            // Reassemble file
            $finalPath = storage_path('app/' . self::CHUNK_UPLOAD_DIR . '/' . $sessionId . '_' . time() . '_' . $fileName);
            $finalDir = dirname($finalPath);
            
            if (!is_dir($finalDir)) {
                mkdir($finalDir, 0755, true);
            }

            $finalFile = fopen($finalPath, 'wb');
            if (!$finalFile) {
                throw new \Exception('Cannot create final file');
            }

            foreach ($chunks as $chunk) {
                $chunkContent = fread(fopen($chunk, 'rb'), filesize($chunk));
                fwrite($finalFile, $chunkContent);
            }
            fclose($finalFile);

            // Verify file integrity
            if (!file_exists($finalPath) || filesize($finalPath) === 0) {
                throw new \Exception('Final file is empty or corrupted');
            }

            Log::info('File reassembled successfully', [
                'sessionId' => $sessionId,
                'fileName' => $fileName,
                'finalSize' => filesize($finalPath),
                'chunkCount' => count($chunks)
            ]);

            // Import the file
            try {
                Excel::import(new ItemsTransactionsImport(), $finalPath);
                
                // Clean up chunks after successful import
                $this->cleanupSessionFiles($sessionDir);

                return response()->json([
                    'success' => true,
                    'message' => 'تم رفع الملف ومعالجته بنجاح'
                ]);

            } catch (\Exception $importError) {
                Log::error('Import error after reassembly', [
                    'sessionId' => $sessionId,
                    'error' => $importError->getMessage(),
                    'file' => $finalPath
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في معالجة الملف: ' . $importError->getMessage()
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('Finalize upload error', [
                'error' => $e->getMessage(),
                'sessionId' => $request->input('sessionId') ?? 'unknown',
                'file' => $request->input('fileName') ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنهاء العملية: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Clean up temporary chunk files
     */
    private function cleanupSessionFiles($sessionDir)
    {
        if (is_dir($sessionDir)) {
            $files = glob($sessionDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            @rmdir($sessionDir);
        }
    }
}

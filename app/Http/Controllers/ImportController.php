<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsTransactionsImport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        // زيادة الـ memory والوقت للملفات الكبيرة
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '0');

        try {
            // Get the raw file upload without validation
            $files = $request->files->all();
            $file = $files['file'] ?? null;

            if (!$file) {
                Log::error('Import error: No file provided in request');
                return back()->with('error', 'لم يتم تحديد ملف');
            }

            // Log full request details
            Log::info('Upload attempt', [
                'method' => $request->getMethod(),
                'content_length' => $request->server('CONTENT_LENGTH'),
                'files_count' => count($files)
            ]);

            // Get file extension before any other operations
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['xlsx', 'xls', 'csv'];
            if (!in_array($extension, $allowedExtensions)) {
                Log::error('Invalid file extension', ['extension' => $extension]);
                return back()->with('error', 'نوع الملف غير مدعوم. الملفات المدعومة: xlsx, xls, csv');
            }

            // Check if file is accessible even if it's empty
            try {
                $fileSize = $file->getSize();
                Log::info('File size check', ['size' => $fileSize]);
            } catch (\Throwable $e) {
                Log::error('Could not determine file size', [
                    'error' => $e->getMessage(),
                    'file_error_code' => $file->getError()
                ]);
                return back()->with('error', 'لم يتم رفع الملف بشكل صحيح. خطأ: ' . $e->getMessage());
            }

            // Check upload error code
            if ($file->getError() !== UPLOAD_ERR_OK) {
                $errorMsg = $this->getUploadErrorMessage($file->getError());
                Log::error('File upload error at PHP level', [
                    'error_code' => $file->getError(),
                    'error_message' => $errorMsg,
                    'file_size' => $file->getSize() ?? 'unknown'
                ]);
                return back()->with('error', 'خطأ في رفع الملف: ' . $errorMsg);
            }

            // Validate file size (100MB max)
            if ($fileSize > 104857600) {
                Log::error('File too large', ['size' => $fileSize]);
                return back()->with('error', 'حجم الملف كبير جداً. الحد الأقصى: 100MB');
            }

            // Try to get the real path
            $realPath = null;
            try {
                $realPath = $file->getRealPath();
                Log::info('Got real file path', ['path' => $realPath]);
            } catch (\Throwable $e) {
                Log::warning('Could not get real path', ['error' => $e->getMessage()]);
            }

            if (!$realPath || !file_exists($realPath)) {
                Log::error('File does not exist at real path', [
                    'path' => $realPath,
                    'exists' => file_exists($realPath ?? '')
                ]);
                return back()->with('error', 'الملف غير متاح للقراءة');
            }

            Log::info('File validation passed, starting import', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $fileSize,
                'real_path' => $realPath,
                'extension' => $extension
            ]);

            // Import the file
            Excel::import(
                new ItemsTransactionsImport,
                $realPath
            );

            Log::info('Import completed successfully');
            return back()->with('success', 'تم استيراد البيانات بنجاح');
        } catch (\Throwable $e) {
            Log::error('Import error occurred', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    private function getUploadErrorMessage($errorCode)
    {
        $errors = [
            UPLOAD_ERR_OK => 'لا يوجد خطأ',
            UPLOAD_ERR_INI_SIZE => 'الملف أكبر من upload_max_filesize في php.ini',
            UPLOAD_ERR_FORM_SIZE => 'الملف أكبر من MAX_FILE_SIZE في النموذج',
            UPLOAD_ERR_PARTIAL => 'تم رفع الملف بشكل جزئي فقط',
            UPLOAD_ERR_NO_FILE => 'لم يتم تحديد ملف للرفع',
            UPLOAD_ERR_NO_TMP_DIR => 'لا توجد مجلد مؤقت لحفظ الملف',
            UPLOAD_ERR_CANT_WRITE => 'فشل في الكتابة إلى القرص الصلب',
            UPLOAD_ERR_EXTENSION => 'توقفت ملحقات PHP عملية الرفع',
        ];

        return $errors[$errorCode] ?? 'خطأ غير معروف في الرفع';
    }
}

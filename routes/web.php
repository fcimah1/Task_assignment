<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ChunkedUploadController;
use Illuminate\Http\Request;
/*
/*|--------------------------------------------------------------------------

/*| Web Routes
/*|--------------------------------------------------------------------------
/*| Here is where you can register web routes for your application. These
/*| routes are loaded by the RouteServiceProvider within a group which
/*| contains the "web" middleware group. Now create something great!
/*|-------------------------------------------------------------------*/
Route::get('/', function () {
    return view('welcome');
});

// Original import routes
Route::post('/import', [ImportController::class, 'import']);
Route::get('/import', function () {
    return view('import_chunked'); // Use new chunked upload interface
});

// Chunked upload routes (new approach to bypass upload limits)
Route::post('/import/chunk-upload', [ChunkedUploadController::class, 'uploadChunk']);
Route::post('/import/finalize-upload', [ChunkedUploadController::class, 'finalizeUpload']);
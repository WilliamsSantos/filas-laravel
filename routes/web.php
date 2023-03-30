<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DocumentsController::class, 'index'])
    ->name('index');

Route::get('/process', [DocumentsController::class, 'processFile'])
    ->name('process');

Route::prefix('queue')->group(function () {
    Route::get('/process', [DocumentsController::class, 'runQueue'])
        ->name('queue.run');
});

Route::post('/documents/upload', [DocumentsController::class, 'upload'])
    ->name('documents.upload');

<?php

use App\Http\Controllers\PincodeJsonController;
use App\Http\Controllers\QrCodeDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/json', PincodeJsonController::class)->name('pincodes.json');

Route::get('/qrcodes', [QrCodeDownloadController::class, 'index'])->name('qrcodes.index');
Route::get('/qrcodes/{stateCode}/download', [QrCodeDownloadController::class, 'download'])
    ->where('stateCode', '[A-Za-z]{3}')
    ->name('qrcodes.download');
Route::get('/qrcodes/{stateCode}/download/pdf', [QrCodeDownloadController::class, 'downloadPdf'])
    ->where('stateCode', '[A-Za-z]{3}')
    ->name('qrcodes.download.pdf');

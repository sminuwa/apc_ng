<?php

use App\Http\Controllers\PincodeApiController;
use Illuminate\Support\Facades\Route;

Route::get('/fetch', [PincodeApiController::class, 'fetch']);
Route::post('/fetch', [PincodeApiController::class, 'fetch']);
Route::post('/push', [PincodeApiController::class, 'push']);

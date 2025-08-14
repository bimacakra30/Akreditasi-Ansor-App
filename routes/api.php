<?php

use App\Http\Controllers\Api\AkreditasiController;
use Illuminate\Support\Facades\Route;


Route::post('/akreditasi', [AkreditasiController::class, 'store']);
Route::get('/akreditasi', [AkreditasiController::class, 'index']);
Route::get('/akreditasi/{akreditasi}', [AkreditasiController::class, 'show']);

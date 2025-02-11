<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PelangganDataController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\PenyewaanDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================
// AUTHENTICATION ROUTES
// ==========================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// ==========================
// ADMIN AUTH ROUTES
// ==========================
Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/me', [AdminAuthController::class, 'me']);
    });
});

// ==========================
// AUTHENTICATED USER ROUTES
// ==========================
Route::middleware('auth:api,admin')->group(function () {
    Route::apiResource('/pelanggan', PelangganController::class);
    Route::apiResource('/penyewaan', PenyewaanController::class);
    Route::apiResource('/kategori', KategoriController::class);
    Route::apiResource('/alat', AlatController::class);
    Route::apiResource('/penyewaan_detail', PenyewaanDetailController::class);
    Route::apiResource('/pelanggan_data', PelangganDataController::class);
});

// ==========================
// USER INFO ROUTE (AUTH SANCTUM)
// ==========================
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

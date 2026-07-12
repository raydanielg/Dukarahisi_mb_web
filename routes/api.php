<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Webhook route (no auth)
Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Catalog routes
    Route::get('/catalog/levels', [CatalogController::class, 'levels']);
    Route::get('/catalog/classes/{level}', [CatalogController::class, 'classes']);
    Route::get('/catalog/subjects/{classRoom}', [CatalogController::class, 'subjects']);
    Route::get('/catalog/notes/{subject}', [CatalogController::class, 'notes']);
    Route::get('/catalog/note/{note}', [CatalogController::class, 'note']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);

    // Payments
    Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
});

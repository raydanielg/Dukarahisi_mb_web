<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\NotificationController;
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
    Route::get('/catalog/topics/{subjectId}', [CatalogController::class, 'topics']);
    Route::get('/catalog/materials/{topicId}', [CatalogController::class, 'materials']);
    Route::get('/catalog/materials/{type}/{id}/download', [CatalogController::class, 'downloadMaterial'])->name('api.materials.download');

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/single', [OrderController::class, 'storeSingle']);
    Route::get('/orders/{order}/status', [OrderController::class, 'status']);

    // Payments
    Route::post('/payments/initiate', [PaymentController::class, 'initiate']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

    // Activities
    Route::get('/activities', [ActivityController::class, 'index']);
});

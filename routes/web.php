<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CatalogManageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MaterialManageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/home', function () {
        return redirect('/dashboard');
    })->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::redirect('/admin', '/dashboard');

    // Analytics
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');

    // Catalog management
    Route::prefix('admin/catalog')->name('admin.catalog.')->group(function () {
        Route::get('/levels', [CatalogManageController::class, 'levelsIndex'])->name('levels');
        Route::post('/levels', [CatalogManageController::class, 'levelsStore'])->name('levels.store');
        Route::put('/levels/{level}', [CatalogManageController::class, 'levelsUpdate'])->name('levels.update');
        Route::delete('/levels/{level}', [CatalogManageController::class, 'levelsDestroy'])->name('levels.destroy');

        Route::get('/classes', [CatalogManageController::class, 'classesIndex'])->name('classes');
        Route::post('/classes', [CatalogManageController::class, 'classesStore'])->name('classes.store');
        Route::put('/classes/{classRoom}', [CatalogManageController::class, 'classesUpdate'])->name('classes.update');
        Route::delete('/classes/{classRoom}', [CatalogManageController::class, 'classesDestroy'])->name('classes.destroy');

        Route::get('/subjects', [CatalogManageController::class, 'subjectsIndex'])->name('subjects');
        Route::post('/subjects', [CatalogManageController::class, 'subjectsStore'])->name('subjects.store');
        Route::put('/subjects/{subject}', [CatalogManageController::class, 'subjectsUpdate'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [CatalogManageController::class, 'subjectsDestroy'])->name('subjects.destroy');

        Route::get('/topics', [CatalogManageController::class, 'topicsIndex'])->name('topics');
        Route::post('/topics', [CatalogManageController::class, 'topicsStore'])->name('topics.store');
        Route::put('/topics/{topic}', [CatalogManageController::class, 'topicsUpdate'])->name('topics.update');
        Route::delete('/topics/{topic}', [CatalogManageController::class, 'topicsDestroy'])->name('topics.destroy');
    });

    // Materials management
    foreach (['notes', 'books', 'lesson-notes', 'lesson-plans', 'syllabus', 'schemes', 'logbooks'] as $type) {
        Route::get("/materials/$type", function (\Illuminate\Http\Request $request) use ($type) {
            return app(MaterialManageController::class)->index($type, $request);
        })->name("admin.materials.$type");
        Route::post("/materials/$type", function (\Illuminate\Http\Request $request) use ($type) {
            return app(MaterialManageController::class)->store($type, $request);
        })->name("admin.materials.$type.store");
        Route::put("/materials/$type/{id}", function (\Illuminate\Http\Request $request, $id) use ($type) {
            return app(MaterialManageController::class)->update($type, $id, $request);
        })->name("admin.materials.$type.update");
        Route::delete("/materials/$type/{id}", function (\Illuminate\Http\Request $request, $id) use ($type) {
            return app(MaterialManageController::class)->destroy($type, $id, $request);
        })->name("admin.materials.$type.destroy");
    }

    // Sales management
    Route::prefix('admin/sales')->name('admin.sales.')->group(function () {
        Route::get('/orders', [SalesController::class, 'ordersIndex'])->name('orders');
        Route::patch('/orders/{order}/status', [SalesController::class, 'ordersUpdateStatus'])->name('orders.status');
        Route::get('/customers', [SalesController::class, 'customersIndex'])->name('customers');
        Route::get('/payments', [SalesController::class, 'paymentsIndex'])->name('payments');
        Route::patch('/payments/{payment}/status', [SalesController::class, 'paymentsUpdateStatus'])->name('payments.status');
        Route::get('/reviews', [SalesController::class, 'reviewsIndex'])->name('reviews');
        Route::delete('/reviews/{review}', [SalesController::class, 'reviewsDestroy'])->name('reviews.destroy');
    });

    // Profile
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
    Route::post('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/admin/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');
    Route::post('/admin/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('admin.profile.avatar');

    // Settings
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/admin/settings/clear-cache', [SettingController::class, 'clearCache'])->name('admin.settings.clear-cache');
});

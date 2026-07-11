<?php

use App\Http\Controllers\Admin\CatalogManageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NoteController;
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
    });

    // Notes management
    Route::resource('admin/notes', NoteController::class)->names('admin.notes');

    // Settings
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
});

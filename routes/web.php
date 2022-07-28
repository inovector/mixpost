<?php

use Illuminate\Support\Facades\Route;
use Lao9s\Mixpost\Http\Middleware\HandleInertiaRequests;
use Lao9s\Mixpost\Http\Controllers\DashboardController;
use Lao9s\Mixpost\Http\Controllers\AccountsController;
use Lao9s\Mixpost\Http\Controllers\SettingsController;
use Lao9s\Mixpost\Http\Controllers\PostsController;
use Lao9s\Mixpost\Http\Controllers\ScheduleController;
use Lao9s\Mixpost\Http\Controllers\MediaController;

Route::middleware(['web', HandleInertiaRequests::class])->prefix('mixpost')->name('mixpost.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('accounts', [AccountsController::class, 'index'])->name('accounts');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::prefix('posts')->name('posts.')->group(function() {
        Route::get('/', [PostsController::class, 'index'])->name('index');
        Route::get('create', [PostsController::class, 'create'])->name('create');
        Route::post('store', [PostsController::class, 'store'])->name('store');
    });
    Route::get('schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::get('media', [MediaController::class, 'index'])->name('media');
});

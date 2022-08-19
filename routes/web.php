<?php

use Illuminate\Support\Facades\Route;
use Inovector\Mixpost\Http\Middleware\HandleInertiaRequests;
use Inovector\Mixpost\Http\Controllers\DashboardController;
use Inovector\Mixpost\Http\Controllers\AccountsController;
use Inovector\Mixpost\Http\Controllers\AddAccountController;
use Inovector\Mixpost\Http\Controllers\SettingsController;
use Inovector\Mixpost\Http\Controllers\PostsController;
use Inovector\Mixpost\Http\Controllers\ScheduleController;
use Inovector\Mixpost\Http\Controllers\MediaController;
use Inovector\Mixpost\Http\Controllers\CallbackSocialProviderController;

Route::middleware(['web', HandleInertiaRequests::class])->prefix('mixpost')->name('mixpost.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountsController::class, 'index'])->name('index');
        Route::post('add/{provider}', AddAccountController::class)->name('add');
        Route::put('update/{account}', [AccountsController::class, 'update'])->name('update');
        Route::delete('{account}', [AccountsController::class, 'delete'])->name('delete');
    });

    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostsController::class, 'index'])->name('index');
        Route::get('create', [PostsController::class, 'create'])->name('create');
        Route::post('store', [PostsController::class, 'store'])->name('store');
        Route::put('{post}', [PostsController::class, 'update'])->name('update');
        Route::delete('{post}', [PostsController::class, 'destroy'])->name('delete');
    });

    Route::get('schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::get('media', [MediaController::class, 'index'])->name('media');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');

    Route::get('callback/{provider}', CallbackSocialProviderController::class);
});

<?php

use Illuminate\Support\Facades\Route;
use Inovector\Mixpost\Http\Middleware\HandleInertiaRequests;
use Inovector\Mixpost\Http\Middleware\Auth as MixpostAuthMiddleware;
use Inovector\Mixpost\Http\Controllers\DashboardController;
use Inovector\Mixpost\Http\Controllers\AccountsController;
use Inovector\Mixpost\Http\Controllers\AccountEntitiesController;
use Inovector\Mixpost\Http\Controllers\AddAccountController;
use Inovector\Mixpost\Http\Controllers\SettingsController;
use Inovector\Mixpost\Http\Controllers\PostsController;
use Inovector\Mixpost\Http\Controllers\DuplicatePostController;
use Inovector\Mixpost\Http\Controllers\SchedulePostController;
use Inovector\Mixpost\Http\Controllers\DeletePostsController;
use Inovector\Mixpost\Http\Controllers\TagsController;
use Inovector\Mixpost\Http\Controllers\ScheduleController;
use Inovector\Mixpost\Http\Controllers\MediaController;
use Inovector\Mixpost\Http\Controllers\MediaUploadFileController;
use Inovector\Mixpost\Http\Controllers\CallbackSocialProviderController;

Route::middleware(['web', MixpostAuthMiddleware::class, HandleInertiaRequests::class])->prefix('mixpost')->name('mixpost.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountsController::class, 'index'])->name('index');
        Route::post('add/{provider}', AddAccountController::class)->name('add');
        Route::put('update/{account}', [AccountsController::class, 'update'])->name('update');
        Route::delete('{account}', [AccountsController::class, 'delete'])->name('delete');

        Route::prefix('entities')->name('entities.')->group(function () {
            Route::get('{provider}', [AccountEntitiesController::class, 'index'])->name('index');
            Route::post('{provider}', [AccountEntitiesController::class, 'store'])->name('store');
        });
    });

    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostsController::class, 'index'])->name('index');
        Route::get('create', [PostsController::class, 'create'])->name('create');
        Route::post('store', [PostsController::class, 'store'])->name('store');
        Route::get('{post}', [PostsController::class, 'edit'])->name('edit');
        Route::put('{post}', [PostsController::class, 'update'])->name('update');
        Route::delete('{post}', [PostsController::class, 'destroy'])->name('delete');

        Route::post('schedule/{post}', SchedulePostController::class)->name('schedule');
        Route::post('duplicate/{post}', DuplicatePostController::class)->name('duplicate');
        Route::delete('/', DeletePostsController::class)->name('multipleDelete');
    });

    Route::get('schedule', [ScheduleController::class, 'index'])->name('schedule');

    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::get('fetch', [MediaController::class, 'fetch'])->name('fetch');
        Route::post('upload', MediaUploadFileController::class)->name('upload');
    });

    Route::prefix('tags')->name('tags.')->group(function () {
        Route::post('/', [TagsController::class, 'store'])->name('store');
        Route::put('{tag}', [TagsController::class, 'update'])->name('update');
        Route::delete('{tag}', [TagsController::class, 'destroy'])->name('delete');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });

    Route::get('callback/{provider}', CallbackSocialProviderController::class)->name('callbackSocialProvider');
});

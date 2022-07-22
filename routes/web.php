<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Lao9s\Mixpost\Http\Middleware\HandleInertiaRequests;

Route::middleware(['web', HandleInertiaRequests::class])->prefix('mixpost')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    });
});

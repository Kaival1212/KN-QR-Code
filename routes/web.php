<?php

use App\Http\Controllers\QrCodeDownloadController;
use App\Http\Controllers\QrRedirectController;
use Illuminate\Support\Facades\Route;

// Public QR redirect — rate limited to prevent analytics abuse
Route::get('/qr/{slug}', [QrRedirectController::class, 'redirect'])
    ->middleware('throttle:60,1')
    ->name('qr.redirect');

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    // QR code download (auth protected) — format: svg (default) or png
    Route::get('qr-codes/{qrCode}/download/{format?}', [QrCodeDownloadController::class, 'download'])
        ->where('format', 'svg|png')
        ->name('qr-codes.download');

    // Admin-only routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::livewire('clients', 'pages::admin.clients')->name('clients');
        Route::livewire('qr-codes', 'pages::admin.qr-codes')->name('qr-codes');
    });
});

require __DIR__.'/settings.php';

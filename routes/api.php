<?php

declare(strict_types=1);

use App\Http\Controllers\App\RobloxAssetController;
use App\Http\Controllers\Converter\ConversionFileController;
use App\Http\Controllers\Converter\ConverterApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web,sanctum', 'verified.config', 'throttle:converter'])->prefix('converter')->name('api.converter.')->group(function (): void {
    Route::post('/upload', [ConverterApiController::class, 'upload'])->name('upload');
    Route::post('/process', [ConverterApiController::class, 'process'])->name('process');
    Route::get('/status/{audioFile}', [ConverterApiController::class, 'status'])->name('status');
    Route::get('/download/{audioFile}', [ConverterApiController::class, 'download'])->middleware('signed')->name('download');
    Route::get('/history', [ConverterApiController::class, 'history'])->name('history');
    Route::delete('/history/{audioFile}', [ConverterApiController::class, 'destroy'])->name('destroy');

    Route::get('/files/{conversionFile}/download', [ConversionFileController::class, 'download'])->middleware('signed')->name('files.download');
    Route::post('/files/{conversionFile}/upload-roblox', [ConversionFileController::class, 'uploadRoblox'])->name('files.upload-roblox');
    Route::get('/{audioFile}/download-all', [ConversionFileController::class, 'downloadAll'])->middleware('signed')->name('download-all');
});

Route::middleware(['auth:web,sanctum', 'verified.config', 'throttle:converter'])->prefix('roblox')->name('api.roblox.')->group(function (): void {
    Route::post('/assets/upload', [RobloxAssetController::class, 'upload'])->name('assets.upload');
});

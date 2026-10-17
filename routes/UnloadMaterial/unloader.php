<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\UnloaderController;

Route::get('/unloader-listing', [UnloaderController::class, 'index'])
    ->name('unloader.index');

Route::get('/unloader-return/{vid}/details', [UnloaderController::class, 'returnDetails'])
    ->name('unloader.return-details');

Route::post('/unloader-return', [UnloaderController::class, 'returnMaterial'])
    ->name('unloader.return');

Route::match(['get', 'post'],'/unload-materials/{vid?}', [UnloaderController::class, 'unloadMaterials'])
    ->name('unloader.unload-materials');

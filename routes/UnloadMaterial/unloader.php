<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\UnloaderController;

Route::get('/unloader-listing', [UnloaderController::class, 'index'])
    ->name('unloader.index');

Route::match(['get', 'post'],'/unload-materials/{vid?}', [UnloaderController::class, 'unloadMaterials'])
    ->name('unloader.unload-materials');

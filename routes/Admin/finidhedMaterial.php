<?php
use App\Http\Controllers\Admin\FinidhedMaterialController;
use Illuminate\Support\Facades\Route;

Route::get('/finished-material', [FinidhedMaterialController::class, 'index'])
    ->name('finishedMaterial');

Route::get('/finished-material/{id}', [FinidhedMaterialController::class, 'view'])->whereNumber('id')
    ->name('finishedMaterial.view');
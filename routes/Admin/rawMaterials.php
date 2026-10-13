<?php

use App\Http\Controllers\Admin\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::get('/raw-materials', [PurchaseController::class, 'indexRM'])
    ->name('rawMaterials');
    
Route::get('/raw-material/{id}/view', [PurchaseController::class, 'viewRM'])->whereNumber('id')
    ->name('rawMaterials.view');
    
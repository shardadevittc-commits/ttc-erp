<?php

use App\Http\Controllers\Admin\PurchaseController;
use Illuminate\Support\Facades\Route;


Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase');
Route::match(['get', 'post'], '/purchase-orders/add', [PurchaseController::class, 'add'])->name('purchase.add');
Route::delete('/purchase/{id}', [PurchaseController::class, 'delete'])->name('purchase.delete');

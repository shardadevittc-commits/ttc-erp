<?php

use App\Http\Controllers\Admin\SaleOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/sale-orders', [SaleOrderController::class, 'index'])
    ->name('saleOrders');
    
Route::match(['get', 'post'], '/sale-orders/add', [SaleOrderController::class, 'add'])
    ->name('saleOrders.add');
    
Route::get('/sale-orders/{id}/view', [SaleOrderController::class, 'view'])->whereNumber('id')
    ->name('saleOrders.view');
    
Route::match(['get', 'post'], '/sale-orders/{id}/edit', [SaleOrderController::class, 'edit'])->whereNumber('id')
    ->name('saleOrders.edit');
    
Route::get('/sale-orders/{id}/delete', [SaleOrderController::class, 'destroy'])->whereNumber('id')
    ->name('saleOrders.delete');
    
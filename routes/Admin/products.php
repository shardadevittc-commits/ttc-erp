<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index'])
    ->name('products');
Route::match(['get', 'post'], '/products/add', [ProductController::class, 'add'])
    ->name('products.add');
Route::get('/products/{id}/view', [ProductController::class, 'view'])->whereNumber('id')
    ->name('products.view');
Route::match(['get', 'post'], '/products/{id}/edit', [ProductController::class, 'edit'])->whereNumber('id')
    ->name('products.edit');
Route::delete('/products/{id}/delete', [ProductController::class, 'destroy'])->whereNumber('id')
    ->name('products.delete');
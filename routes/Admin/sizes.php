<?php

use App\Http\Controllers\Admin\SizeController;
use Illuminate\Support\Facades\Route;

Route::get('/sizes', [SizeController::class, 'index'])
    ->name('sizes');
Route::match(['get', 'post'], '/sizes/add', [SizeController::class, 'add'])
    ->name('sizes.add');
Route::get('/sizes/{id}/view', [SizeController::class, 'view'])->whereNumber('id')
    ->name('sizes.view');
Route::match(['get', 'post'], '/sizes/{id}/edit', [SizeController::class, 'edit'])->whereNumber('id')
    ->name('sizes.edit');
Route::get('/sizes/{id}/delete', [SizeController::class, 'destroy'])->whereNumber('id')
    ->name('sizes.delete');
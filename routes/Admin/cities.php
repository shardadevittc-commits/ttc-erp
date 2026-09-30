<?php

use App\Http\Controllers\Admin\CitiesController;
use Illuminate\Support\Facades\Route;

Route::get('/cities', [CitiesController::class, 'index'])
    ->name('cities');
Route::match(['get', 'post'], '/cities/add', [CitiesController::class, 'add'])
    ->name('cities.add');
Route::get('/cities/{id}/view', [CitiesController::class, 'view'])->whereNumber('id')
    ->name('cities.view');
Route::match(['get', 'post'], '/cities/{id}/edit', [CitiesController::class, 'edit'])->whereNumber('id')
    ->name('cities.edit');
Route::delete('/cities/{id}/delete', [CitiesController::class, 'destroy'])->whereNumber('id')
    ->name('cities.delete');
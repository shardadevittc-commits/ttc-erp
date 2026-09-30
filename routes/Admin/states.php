<?php

use App\Http\Controllers\Admin\StatesController;
use Illuminate\Support\Facades\Route;

Route::get('/states', [StatesController::class, 'index'])
    ->name('states');
Route::match(['get', 'post'], '/states/add', [StatesController::class, 'add'])
    ->name('states.add');
Route::get('/states/{id}/view', [StatesController::class, 'view'])->whereNumber('id')
    ->name('states.view');
Route::match(['get', 'post'], '/states/{id}/edit', [StatesController::class, 'edit'])->whereNumber('id')
    ->name('states.edit');
Route::delete('/states/{id}/delete', [StatesController::class, 'destroy'])->whereNumber('id')
    ->name('states.delete');
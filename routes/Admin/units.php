<?php

use App\Http\Controllers\Admin\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/units', [UnitController::class, 'index'])
    ->name('units');
Route::match(['get', 'post'], '/units/add', [UnitController::class, 'add'])
    ->name('units.add');
Route::get('/units/{id}/view', [UnitController::class, 'view'])->whereNumber('id')
    ->name('units.view');
Route::match(['get', 'post'], '/units/{id}/edit', [UnitController::class, 'edit'])->whereNumber('id')
    ->name('units.edit');
Route::delete('/units/{id}/delete', [UnitController::class, 'destroy'])->whereNumber('id')
    ->name('units.delete');
<?php

use App\Http\Controllers\Admin\CountriesController;
use Illuminate\Support\Facades\Route;

Route::get('/countries', [CountriesController::class, 'index'])
    ->name('countries');
Route::match(['get', 'post'], '/countries/add', [CountriesController::class, 'add'])
    ->name('countries.add');
Route::get('/countries/{id}/view', [CountriesController::class, 'view'])->whereNumber('id')
    ->name('countries.view');
Route::match(['get', 'post'], '/countries/{id}/edit', [CountriesController::class, 'edit'])->whereNumber('id')
    ->name('countries.edit');
Route::delete('/countries/{id}/delete', [CountriesController::class, 'destroy'])->whereNumber('id')
    ->name('countries.delete');
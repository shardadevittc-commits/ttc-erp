<?php

use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/customers', [CustomerController::class, 'index'])
    ->name('customers');

Route::match(['get', 'post'], '/customers/add', [CustomerController::class, 'add'])
    ->name('customers.add');

Route::get('/customers/location/states', [CustomerController::class, 'statesByCountry'])
    ->name('customers.location.states');

Route::get('/customers/location/cities', [CustomerController::class, 'citiesByState'])
    ->name('customers.location.cities');

Route::get('/customers/gst-lookup/{gstin}', [CustomerController::class, 'verifyGst'])
    ->where('gstin', '[A-Za-z0-9]{15}')
    ->name('customers.gst-lookup');

Route::get('/customers/{id}/view', [CustomerController::class, 'view'])->whereNumber('id')
    ->name('customers.view');

Route::match(['get', 'post'], '/customers/{id}/edit', [CustomerController::class, 'edit'])->whereNumber('id')
    ->name('customers.edit');

Route::get('/customers/{id}/delete', [CustomerController::class, 'destroy'])->whereNumber('id')
    ->name('customers.delete');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AccountController;

Route::get('/accounts', [AccountController::class, 'index'])
    ->name('accounts');

Route::get('/accounts/{gate}/add-invoice', [AccountController::class, 'addInvoice'])
    ->whereNumber('gate')
    ->name('accounts.addInvoice');

Route::get('/accounts/{gate}', [AccountController::class, 'show'])
    ->whereNumber('gate')
    ->name('accounts.view');

Route::post('/accounts/invoice', [AccountController::class, 'storeInvoice'])
    ->name('accounts.invoice.store');

Route::post('/accounts/return', [AccountController::class, 'storeReturn'])
    ->name('accounts.return.store');

Route::get('/accounts-history', [AccountController::class, 'index'])
    ->name('accounts.history');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AccountController;

Route::get('/accounts', [AccountController::class, 'index'])
    ->name('accounts');

Route::get('/accounts/{gate}/add-invoice', [AccountController::class, 'addInvoice'])
    ->whereNumber('gate')
    ->name('accounts.addInvoice');

Route::get('/accounts/{gate}/invoice-bill', [AccountController::class, 'invoiceBill'])
    ->whereNumber('gate')
    ->name('accounts.invoice.bill');

Route::get('/accounts/{gate}', [AccountController::class, 'show'])
    ->whereNumber('gate')
    ->name('accounts.view');

Route::post('/accounts/invoice', [AccountController::class, 'storeInvoice'])
    ->name('accounts.invoice.store');

Route::post('/accounts/return', [AccountController::class, 'storeReturn'])
    ->name('accounts.return.store');

Route::get('/scrap-dispatch', [AccountController::class, 'scrapDispatch'])
    ->name('scrapDispatch.index');

Route::post('/scrap-dispatch', [AccountController::class, 'storeScrapDispatch'])
    ->name('scrapDispatch.store');

Route::get('/scrap-dispatch/history', [AccountController::class, 'scrapDispatchHistory'])
    ->name('scrapDispatch.history');

Route::get('/scrap-dispatch/{scrapDispatch}/invoice', [AccountController::class, 'scrapDispatchInvoice'])
    ->whereNumber('scrapDispatch')
    ->name('scrapDispatch.invoice');

Route::get('/accounts-history', [AccountController::class, 'index'])
    ->name('accounts.history');

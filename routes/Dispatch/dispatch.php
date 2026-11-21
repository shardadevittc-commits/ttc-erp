<?php

use App\Http\Controllers\DispatchController;
use Illuminate\Support\Facades\Route;

Route::get('/dispatch', [DispatchController::class, 'index'])->name('dispatch.index');
Route::get('/dispatch/history', [DispatchController::class, 'history'])->name('dispatch.history');
Route::get('/dispatch/history/{dispatchMaterial}', [DispatchController::class, 'historyDetails'])
    ->whereNumber('dispatchMaterial')
    ->name('dispatch.history.show');
Route::get('/dispatch/{gate}/pending-orders', [DispatchController::class, 'pendingOrders'])->name('manage-dispatch.pending-orders');
Route::get('/dispatch/truck/{gate}', [DispatchController::class, 'truckOrders'])->name('dispatch.truck-orders');
Route::get('/dispatch/selected-orders', [DispatchController::class, 'selectedOrders'])->name('dispatch.selected-orders.form');
Route::post('/dispatch/send-for-weight', [DispatchController::class, 'sendForWeight'])->name('dispatch.send-for-weight');
Route::post('/dispatch/material', [DispatchController::class, 'store'])->name('dispatch.material.store');

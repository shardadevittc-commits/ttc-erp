<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DispatchController;

Route::get('/dispatch', [DispatchController::class, 'index'])->name('dispatch.index');
Route::get('pending-orders/{cust_id}',[DispatchController::class, 'pendingOrders'])->name('manage-dispatch.pending-orders');
Route::post('/dispatch/selected-orders', [DispatchController::class, 'selectedOrders'])->name('dispatch.selected-orders');

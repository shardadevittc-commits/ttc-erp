<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DispatchController;

Route::get('/dispatch', [DispatchController::class, 'index'])->name('dispatch.index');

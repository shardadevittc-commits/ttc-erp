<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeightController;

Route::get('/weight', [WeightController::class, 'index'])
    ->name('weight.index');

Route::match(['get', 'post'],'/material_weight', [WeightController::class, 'add'])
    ->name('material_weight');
    
Route::match(['get', 'post'],'/weight/history', [WeightController::class, 'history'])
    ->name('weight.history');

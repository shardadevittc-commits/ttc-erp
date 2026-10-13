<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GateController;

Route::get('/gate', [GateController::class, 'index'])
    ->name('gate.index');

Route::match(['get', 'post'],'/material_in', [GateController::class, 'add'])
    ->name('material_in');
    
Route::delete('/material-in/{id}', [GateController::class, 'delete'])
    ->name('material_in.delete');

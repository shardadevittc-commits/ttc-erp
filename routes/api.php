<?php

use App\Http\Controllers\Api\GateController;
use App\Http\Controllers\Api\WeightController;

Route::post('/gate/add', [GateController::class, 'add']);
Route::delete('/gate/delete/{id}', [GateController::class, 'delete']);

Route::match(['get', 'post'], '/weight/add', [WeightController::class, 'add']);

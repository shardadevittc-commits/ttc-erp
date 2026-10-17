<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LabController;

Route::get('/lab', [LabController::class, 'index'])->name('lab.index');
Route::post('/lab-results/store', [LabController::class, 'store'])->name('lab-results.store');

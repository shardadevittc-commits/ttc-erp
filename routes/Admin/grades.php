<?php

use App\Http\Controllers\Admin\GradeController;
use Illuminate\Support\Facades\Route;

Route::get('/grades', [GradeController::class, 'index'])
    ->name('grades');
Route::match(['get', 'post'], '/grades/add', [GradeController::class, 'add'])
    ->name('grades.add');
Route::get('/grades/{id}/view', [GradeController::class, 'view'])->whereNumber('id')
    ->name('grades.view');
Route::match(['get', 'post'], '/grades/{id}/edit', [GradeController::class, 'edit'])->whereNumber('id')
    ->name('grades.edit');
Route::delete('/grades/{id}/delete', [GradeController::class, 'destroy'])->whereNumber('id')
    ->name('grades.delete');
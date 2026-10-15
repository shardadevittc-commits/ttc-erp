<?php

use App\Http\Controllers\Admin\GradeController;
use Illuminate\Support\Facades\Route;

Route::get('/grades', [GradeController::class, 'index'])->name('grades');
Route::match(['get', 'post'], '/grades/add', [GradeController::class, 'add'])->name('grades.add');
Route::get('/grades/{id}/view', [GradeController::class, 'view'])->whereNumber('id')->name('grades.view');
Route::match(['get', 'post'], '/grades/{id}/edit', [GradeController::class, 'edit'])->whereNumber('id')->name('grades.edit');
Route::delete('/grades/{id}/delete', [GradeController::class, 'destroy'])->whereNumber('id')->name('grades.delete');

// create new route for grade composition
Route::get('/grade_composition', [GradeController::class, 'composition'])->name('grades.composition');
Route::match(['get', 'post'], '/grade_composition/add', [GradeController::class, 'grade_composition_add'])->name('grades.composition.add');

// create new route for grade composition properties
Route::get('/grade_properties', [GradeController::class, 'properties'])->name('grade_properties');
Route::match(['get', 'post'], '/grade_properties/add', [GradeController::class, 'grade_properties_add'])->name('grade_properties.add');

// create new route for grade composition properties min and max values
Route::get('/grade_properties/min_max', [GradeController::class, 'grade_min_max'])->name('grade_min_max');
Route::match(['get', 'post'], '/grade_properties/min_max_add', [GradeController::class, 'grade_add'])->name('grade_add');

<?php
Route::get('/brands', '\App\Http\Controllers\Admin\BrandController@index')
    ->name('brands');

Route::match(['get', 'post'], '/brands/add', '\App\Http\Controllers\Admin\BrandController@add')
    ->name('brands.add');

Route::get('/brands/{id}/view', '\App\Http\Controllers\Admin\BrandController@view')
    ->name('brands.view');

Route::match(['get', 'post'], '/brands/{id}/edit', '\App\Http\Controllers\Admin\BrandController@edit')
    ->name('brands.edit');

Route::post('/brands/bulkActions/{action}', '\App\Http\Controllers\Admin\BrandController@bulkActions')
    ->name('brands.bulkActions');

Route::get('/brands/{id}/delete', '\App\Http\Controllers\Admin\BrandController@destroy')
    ->name('brands.delete');

Route::get('/brands/trash', '\App\Http\Controllers\Admin\BrandController@trash')
    ->name('brands.trash');

Route::get('/brands/{id}/restore', '\App\Http\Controllers\Admin\BrandController@restore')
    ->name('brands.restore');

Route::get('/brands/{id}/trash-delete', '\App\Http\Controllers\Admin\BrandController@trashDelete')
    ->name('brands.trashDelete');

Route::get('/brands/empty-trash', '\App\Http\Controllers\Admin\BrandController@emptyTrash')
    ->name('brands.emptyTrash');
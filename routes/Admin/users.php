<?php
// Route::resource('users', UserController::class);
Route::match(['get', 'post'], '/users', 'App\Http\Controllers\Admin\UserController@index')
    ->name('users.users');
    
Route::match(['get','post'],'/user/add', '\App\Http\Controllers\Admin\UserController@add')
    ->name('users.add');

Route::match(['get','post'],'/user/edit/{id}', '\App\Http\Controllers\Admin\UserController@edit')
    ->name('users.edit');


Route::match(['get','post'],'/user/view/{id}', '\App\Http\Controllers\Admin\UserController@view')
    ->name('users.view');

Route::match(['patch', 'post'], '/users/{user}/toggle-status', 'App\Http\Controllers\Admin\UserController@toggleStatus')
    ->name('users.toggle-status');

Route::get('/add-role', 'App\Http\Controllers\Admin\UserController@create')
    ->name('add-role');

Route::post('/users/bulkActions/{action}', '\App\Http\Controllers\Admin\UsersController@bulkActions')
    ->name('users.bulkActions');

Route::get('/users/{id}/delete', '\App\Http\Controllers\Admin\UsersController@destroy')
    ->name('users.delete');
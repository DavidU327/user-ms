<?php

use \App\Models\Rol;
use Illuminate\Support\Facades\Route;

//Users
Route::middleware(['auth.jwt', 'role:'.Rol::ADMIN])->group(function () {
    Route::get('users', '\App\Http\Controllers\UserIndexController@index')->name('user.index'); //Mostrar usuarios paginados
    Route::get('change_state/{user}', '\App\Http\Controllers\UserChangeStateController@changeState')->name('user.changeState'); //Cambiar estado de habilitado e inhabilitado
    Route::post('search', '\App\Http\Controllers\UserSearchController@search')->name('user.search'); //Buscar usuario
    Route::delete('delete_user/{user}', '\App\Http\Controllers\UserDeleteController@delete')->name('user.delete'); //Eliminar usuario
});

Route::middleware(['auth.jwt', 'role:'.Rol::ADMIN.','.Rol::RECYCLER])->group(function () {
    Route::get('user/{user}', '\App\Http\Controllers\UserIndexController@show')->name('user.show'); //Mostrar usuario por id
    Route::patch('user-points/{user}', '\App\Http\Controllers\UserUpdatePointsController@updatePoints')->name('user.updatePoints'); //Actualizar puntos
});

//Users
Route::middleware(['auth.jwt', 'role:'.Rol::USER])->group(function () {
    Route::patch('user/{user}', '\App\Http\Controllers\UserUpdateController@update')->name('user.update'); //Actualizar usuario
});


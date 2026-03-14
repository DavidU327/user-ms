<?php

use \App\Models\Rol;
use Illuminate\Support\Facades\Route;

//Users
/*Route::middleware(['auth:api'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('user/{user}', '\App\Http\Controllers\UserUpdateController@update')->name('user.update'); //Actualizar usuario
        Route::delete('deleteUser/{user}', '\App\Http\Controllers\UserDeleteController@delete')->name('user.delete'); //Eliminar usuario
    });
});*/

Route::middleware(['auth.jwt', 'role:'.Rol::ADMIN])->group(function () {
    Route::get('users', '\App\Http\Controllers\UserIndexController@index')->name('user.index'); //Mostrar usuarios paginados
    Route::get('change_state/{collector}', '\App\Http\Controllers\UserChangeStateController@changeState')->name('user.changeState'); //Cambiar estado de habilitado e inhabilitado
    Route::post('search', '\App\Http\Controllers\UserSearchController@search')->name('user.search'); //Buscar usuario
});


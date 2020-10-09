<?php

#use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

#Route::get('/', function () {
#    return view('welcome');
#});
//Importante colocar la ruta completa para que se encuentre el controlador
// Route::get('/', 'App\Http\Controllers\ClientesController@index');
// Route::post('/', 'App\Http\Controllers\ClientesController@index');
// Route::put('/', 'App\Http\Controllers\ClientesController@index');
// Route::delete('/', 'App\Http\Controllers\ClientesController@index');
Route::resource('/', 'App\Http\Controllers\ClientesController');
Route::resource('/registroCliente', 'App\Http\Controllers\ClientesController');
Route::resource('/cursos', 'App\Http\Controllers\CursosController');

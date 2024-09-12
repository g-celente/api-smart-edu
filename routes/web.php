<?php

use Illuminate\Support\Facades\Route;

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


Route::get('/', function () {
    return view('welcome');
});

Route::get('/ola', function(){
    return view('ola');
});

//Route::post('/login', 'App\Http\Controllers\LoginController@login');
/*
Route::middleware('authCred')->prefix('/app')->group(function () {
    Route::get('/instituicao', function (){ return response()->json(['Instituicao Logado']);});
    Route::get('/professor', function (){ return response()->json(['Professor Logado']);});
    Route::get('/aluno', function (){ return response()->json(['Aluno Logado']);});
});
*/
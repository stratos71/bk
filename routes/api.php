<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/cotizacion', [App\Http\Controllers\CotizacionController::class, 'cotizacion']);
Route::post('/consulta', [App\Http\Controllers\CotizacionController::class, 'consulta']);
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);
Route::get('/auth', [App\Http\Controllers\Auth\LoginController::class, 'auth']);

Route::post('/respuesta', [App\Http\Controllers\AdminController::class, 'respuesta']);
Route::post('/respuesta2', [App\Http\Controllers\AdminController::class, 'respuesta2']);
Route::post('/respuesta3', [App\Http\Controllers\AdminController::class, 'respuesta3']);
Route::post('/respuesta4', [App\Http\Controllers\AdminController::class, 'respuesta4']);
Route::post('/respuesta5', [App\Http\Controllers\AdminController::class, 'respuesta5']);
Route::post('/respuesta6', [App\Http\Controllers\AdminController::class, 'respuesta6']);
Route::post('/respuesta7', [App\Http\Controllers\AdminController::class, 'respuesta7']);

Route::post('/pedido', [App\Http\Controllers\AdminController::class, 'pedido']);
Route::post('/pedido2', [App\Http\Controllers\AdminController::class, 'pedido2']);
Route::post('/pedido3', [App\Http\Controllers\AdminController::class, 'pedido3']);


Route::get('/index', [App\Http\Controllers\RegistrosController::class, 'index']);
Route::get('/tabla1', [App\Http\Controllers\RegistrosController::class, 'tabla1']);
Route::get('/tabla2', [App\Http\Controllers\RegistrosController::class, 'tabla2']);
Route::get('/tabla3', [App\Http\Controllers\RegistrosController::class, 'tabla3']);
Route::get('/tabla4', [App\Http\Controllers\RegistrosController::class, 'tabla4']);
Route::get('/tabla5', [App\Http\Controllers\RegistrosController::class, 'tabla5']);
Route::get('/tabla6', [App\Http\Controllers\RegistrosController::class, 'tabla6']);
Route::get('/tabla7', [App\Http\Controllers\RegistrosController::class, 'tabla7']);
Route::get('/tabla8', [App\Http\Controllers\RegistrosController::class, 'tabla8']);
Route::get('/tabla9', [App\Http\Controllers\RegistrosController::class, 'tabla9']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

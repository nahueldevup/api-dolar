<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\HistorialController;

// Ruta original de conversión
Route::get('/convertir', [CotizacionController::class, 'convertir']);
// Nuevas rutas para el historial
Route::get('/consultar-manual', [HistorialController::class, 'consultarManual']);
Route::get('/promedio', [HistorialController::class, 'promedio']);
Route::get('/historial', [HistorialController::class, 'historial']);



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

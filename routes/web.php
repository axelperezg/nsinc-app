<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstrategyController;
use App\Http\Controllers\OficioDgncController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta para evaluación de estrategias por coordinadores de sector
Route::post('/estrategy/{estrategy}/evaluar', [EstrategyController::class, 'evaluar'])
    ->name('estrategy.evaluar')
    ->middleware('auth');

// Ruta para descargar oficios DGNC
Route::get('/oficio-dgnc/{oficioDgncDocument}/download', [OficioDgncController::class, 'download'])
    ->name('oficio-dgnc.download')
    ->middleware('auth');

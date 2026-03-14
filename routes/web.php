<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstrategyController;
use App\Http\Controllers\OficioDgncController;

Route::get('/', function () {
    return view('home');
});

// Ruta pública para servir logos de configuración (evita dependencia del symlink de storage)
Route::get('/config-logo/{type}', function ($type) {
    $key = $type === 'right' ? 'pdf.logo_right_path' : 'pdf.logo_path';
    $path = \App\Models\Configuration::get($key);
    if (!$path) {
        abort(404);
    }
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->name('config-logo');

// Ruta para evaluación de estrategias por coordinadores de sector
Route::post('/estrategy/{estrategy}/evaluar', [EstrategyController::class, 'evaluar'])
    ->name('estrategy.evaluar')
    ->middleware('auth');

// Ruta para descargar oficios DGNC
Route::get('/oficio-dgnc/{oficioDgncDocument}/download', [OficioDgncController::class, 'download'])
    ->name('oficio-dgnc.download')
    ->middleware('auth');

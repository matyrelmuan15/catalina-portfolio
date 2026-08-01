<?php

use App\Http\Controllers\Auth\SalirController;
use App\Livewire\Auth\ConfigurarSegundoFactor;
use App\Livewire\Auth\DesafioSegundoFactor;
use App\Livewire\Auth\Ingreso;
use App\Livewire\Auth\RecuperarClave;
use App\Livewire\Auth\RestablecerClave;
use App\Livewire\Panel\Videos\Listado as VideosListado;
use Illuminate\Support\Facades\Route;

// Portfolio público (se completa en routes/publico.php, incluido más abajo).

// Autenticación — abierto
Route::middleware('guest')->group(function () {
    Route::get('/ingresar', Ingreso::class)->name('ingresar');
    Route::get('/clave/recuperar', RecuperarClave::class)->name('clave.recuperar');
    Route::get('/clave/restablecer/{token}', RestablecerClave::class)->name('clave.restablecer');
});

// El desafío del segundo factor ocurre después de validar la clave pero
// antes de abrir sesión: no puede vivir detrás de 'auth' ni de 'guest'.
Route::get('/panel/segundo-factor', DesafioSegundoFactor::class)->name('panel.segundo-factor');
Route::get('/panel/segundo-factor/configurar', ConfigurarSegundoFactor::class)->name('panel.segundo-factor.configurar');

Route::post('/salir', SalirController::class)->middleware('auth')->name('salir');

// Panel de administración
Route::prefix('panel')->middleware('rol.admin')->group(function () {
    Route::redirect('/', '/panel/videos')->name('panel.inicio');
    Route::get('/videos', VideosListado::class)->name('panel.videos');
    Route::view('/clientes', 'panel.proximamente', ['titulo' => 'Clientes'])->name('panel.clientes');
    Route::view('/pedidos', 'panel.proximamente', ['titulo' => 'Pedidos'])->name('panel.pedidos');
    Route::view('/cuenta', 'panel.proximamente', ['titulo' => 'Cuenta'])->name('panel.cuenta');
});

// Portal de clientes
Route::prefix('portal')->middleware('rol.cliente')->group(function () {
    Route::redirect('/', '/portal/calendario')->name('portal.inicio');
    Route::view('/calendario', 'portal.proximamente', ['titulo' => 'Calendario'])->name('portal.calendario');
    Route::view('/publicaciones', 'portal.proximamente', ['titulo' => 'Publicaciones'])->name('portal.publicaciones');
    Route::view('/pedidos', 'portal.proximamente', ['titulo' => 'Mis pedidos'])->name('portal.pedidos');
    Route::view('/metricas', 'portal.proximamente', ['titulo' => 'Métricas'])->name('portal.metricas');
});

require __DIR__.'/publico.php';

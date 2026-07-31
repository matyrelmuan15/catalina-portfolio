<?php

use App\Livewire\Auth\Ingreso;
use App\Livewire\Auth\RecuperarClave;
use App\Livewire\Auth\RestablecerClave;
use App\Livewire\Auth\VerificarSegundoFactor;
use App\Livewire\EnConstruccion;
use App\Livewire\Panel\ConfigurarSegundoFactor;
use App\Livewire\Publico\Portada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', Portada::class)->name('portada');

// ---------------------------------------------------------------- Ingreso --
Route::middleware('guest')->group(function () {
    Route::get('/ingresar', Ingreso::class)->name('ingresar');
    Route::get('/ingresar/verificar', VerificarSegundoFactor::class)->name('ingresar.verificar');
    Route::get('/clave/recuperar', RecuperarClave::class)->name('clave.recuperar');
    Route::get('/clave/restablecer/{token}', RestablecerClave::class)->name('clave.restablecer');
});

Route::post('/salir', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('portada');
})->middleware('auth')->name('salir');

// ------------------------------------------------------------------ Panel --
Route::prefix('panel')->name('panel.')->middleware(['rol.admin', 'segundo.factor'])->group(function () {
    Route::redirect('/', '/panel/videos')->name('inicio');
    Route::get('/videos', EnConstruccion::class)->name('videos')
        ->defaults('titulo', 'Videos')->defaults('descripcion', 'Gestión de videos del portfolio. Se construye en la fase 3.');
    Route::get('/clientes', EnConstruccion::class)->name('clientes')
        ->defaults('titulo', 'Clientes')->defaults('descripcion', 'Alta y ficha de clientes. Se construye en la fase 4.');
    Route::get('/pedidos', EnConstruccion::class)->name('pedidos')
        ->defaults('titulo', 'Pedidos')->defaults('descripcion', 'Bandeja global de pedidos. Se construye en la fase 9.');
    Route::get('/cuenta', EnConstruccion::class)->name('cuenta')
        ->defaults('titulo', 'Cuenta')->defaults('descripcion', 'Datos de la cuenta y preferencias de notificación.');
    Route::get('/cuenta/dos-factores', ConfigurarSegundoFactor::class)->name('cuenta.dos-factores');
});

// ----------------------------------------------------------------- Portal --
Route::prefix('portal')->name('portal.')->middleware('rol.cliente')->group(function () {
    Route::redirect('/', '/portal/calendario')->name('inicio');
    Route::get('/calendario', EnConstruccion::class)->name('calendario')
        ->defaults('titulo', 'Calendario')->defaults('descripcion', 'Tus fechas comprometidas. Se construye en la fase 5.');
    Route::get('/publicaciones', EnConstruccion::class)->name('publicaciones')
        ->defaults('titulo', 'Publicaciones')->defaults('descripcion', 'Resultados de tus publicaciones. Se construye en la fase 6.');
    Route::get('/pedidos', EnConstruccion::class)->name('pedidos')
        ->defaults('titulo', 'Mis pedidos')->defaults('descripcion', 'Pedís contenido y seguís el estado. Se construye en la fase 9.');
    Route::get('/metricas', EnConstruccion::class)->name('metricas')
        ->defaults('titulo', 'Métricas')->defaults('descripcion', 'El rendimiento de tus redes. Se construye en la fase 8.');
});

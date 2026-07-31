<?php

use App\Livewire\Auth\Ingreso;
use App\Livewire\Auth\RecuperarClave;
use App\Livewire\Auth\RestablecerClave;
use App\Livewire\Auth\VerificarSegundoFactor;
use App\Livewire\EnConstruccion;
use App\Livewire\Panel\Clientes\Ficha as PanelClienteFicha;
use App\Livewire\Panel\Clientes\Listado as PanelClientesListado;
use App\Livewire\Panel\ConfigurarSegundoFactor;
use App\Livewire\Panel\Publicaciones\Ficha as PanelPublicacionFicha;
use App\Livewire\Panel\Publicaciones\Listado as PanelPublicacionesListado;
use App\Livewire\Panel\Videos\Listado as PanelVideosListado;
use App\Livewire\Portal\Calendario as PortalCalendario;
use App\Livewire\Portal\Publicaciones as PortalPublicaciones;
use App\Livewire\Publico\Portada;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', Portada::class)->name('portada');

Route::get('/sitemap.xml', function () {
    $actualizadoEl = Video::query()->publicados()->max('updated_at') ?? now();

    $xml = view('publico.sitemap', ['actualizadoEl' => $actualizadoEl])->render();

    return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

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
    Route::get('/videos', PanelVideosListado::class)->name('videos');

    Route::get('/clientes', PanelClientesListado::class)->name('clientes');
    Route::get('/clientes/{cliente}', PanelClienteFicha::class)->name('clientes.ficha');

    Route::get('/publicaciones', PanelPublicacionesListado::class)->name('publicaciones');
    Route::get('/publicaciones/{publicacion}', PanelPublicacionFicha::class)->name('publicaciones.ficha');

    Route::get('/pedidos', EnConstruccion::class)->name('pedidos')
        ->defaults('titulo', 'Pedidos')->defaults('descripcion', 'Bandeja global de pedidos. Se construye en la fase 9.');
    Route::get('/cuenta', EnConstruccion::class)->name('cuenta')
        ->defaults('titulo', 'Cuenta')->defaults('descripcion', 'Datos de la cuenta y preferencias de notificación.');
    Route::get('/cuenta/dos-factores', ConfigurarSegundoFactor::class)->name('cuenta.dos-factores');
});

// ----------------------------------------------------------------- Portal --
Route::prefix('portal')->name('portal.')->middleware('rol.cliente')->group(function () {
    Route::redirect('/', '/portal/calendario')->name('inicio');
    Route::get('/calendario', PortalCalendario::class)->name('calendario');
    Route::get('/publicaciones', PortalPublicaciones::class)->name('publicaciones');
    Route::get('/pedidos', EnConstruccion::class)->name('pedidos')
        ->defaults('titulo', 'Mis pedidos')->defaults('descripcion', 'Pedís contenido y seguís el estado. Se construye en la fase 9.');
    Route::get('/metricas', EnConstruccion::class)->name('metricas')
        ->defaults('titulo', 'Métricas')->defaults('descripcion', 'El rendimiento de tus redes. Se construye en la fase 8.');
});

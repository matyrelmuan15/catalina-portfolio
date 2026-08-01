<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Permite usar los mismos archivos de resources/views/layouts como
        // <x-layouts.panel> en vistas planas, además de con el atributo
        // #[Layout(...)] de los componentes Livewire de página completa.
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');

        // Sin esto, el middleware 'auth' redirige al caer en el fallback de
        // Laravel a route('login'), que no existe en este proyecto: la ruta
        // de ingreso se llama 'ingresar'.
        Authenticate::redirectUsing(fn () => route('ingresar'));

        // Quien ya tiene sesión y entra a /ingresar va directo a su lugar,
        // no a la portada (comportamiento de mockup/portfolio-mockup.html, enrutar()).
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $usuario = $request->user();

            return match (true) {
                $usuario?->esAdmin() => route('panel.inicio'),
                $usuario?->esCliente() => route('portal.inicio'),
                default => route('portfolio.inicio'),
            };
        });
    }
}

<?php

use App\Http\Middleware\AsegurarRolAdmin;
use App\Http\Middleware\AsegurarRolCliente;
use App\Http\Middleware\CachearRespuestaPublica;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sin esto, detrás del proxy de Cloudflare, Laravel ve una única IP
        // para todos los visitantes y el límite de intentos de ingreso los
        // bloquea a todos juntos (docs/02, sección 7.5).
        $proxies = (string) env('TRUSTED_PROXIES', '*');
        $middleware->trustProxies(at: $proxies === '*' ? '*' : array_filter(explode(',', $proxies)));

        $middleware->alias([
            'rol.admin' => AsegurarRolAdmin::class,
            'rol.cliente' => AsegurarRolCliente::class,
            'cache.publico' => CachearRespuestaPublica::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

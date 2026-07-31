<?php

use App\Http\Middleware\AsegurarRolAdmin;
use App\Http\Middleware\AsegurarRolCliente;
use App\Http\Middleware\AsegurarSegundoFactor;
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
        // Cloudflare llega como proxy: sin esto, el límite de intentos de
        // ingreso ve una sola IP y bloquea a todo el mundo junto.
        $proxiesConfiguradas = (string) env('TRUSTED_PROXIES', '*');
        $middleware->trustProxies(
            at: $proxiesConfiguradas === '*' ? '*' : explode(',', $proxiesConfiguradas),
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->alias([
            'rol.admin' => AsegurarRolAdmin::class,
            'rol.cliente' => AsegurarRolCliente::class,
            'segundo.factor' => AsegurarSegundoFactor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

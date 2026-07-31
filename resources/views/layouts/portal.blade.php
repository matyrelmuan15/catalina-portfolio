<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Portal' }} · {{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;6..96,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="armazon">
        <aside class="lateral">
            <div>
                <div class="marca">{{ auth()->user()->cliente->marca }}</div>
                <div class="sub">Portal de cliente</div>
            </div>
            <nav class="menu">
                <a href="{{ route('portal.calendario') }}" class="{{ request()->routeIs('portal.calendario*') ? 'activo' : '' }}">Calendario</a>
                <a href="{{ route('portal.publicaciones') }}" class="{{ request()->routeIs('portal.publicaciones*') ? 'activo' : '' }}">Publicaciones</a>
                <a href="{{ route('portal.pedidos') }}" class="{{ request()->routeIs('portal.pedidos*') ? 'activo' : '' }}">Mis pedidos</a>
                <a href="{{ route('portal.metricas') }}" class="{{ request()->routeIs('portal.metricas*') ? 'activo' : '' }}">Métricas</a>
            </nav>
            <div class="abajo">
                <a href="https://wa.me/542931403502" target="_blank" rel="noopener">Escribir a Catalina</a>
                <form method="POST" action="{{ route('salir') }}">
                    @csrf
                    <button type="submit">Cerrar sesión</button>
                </form>
            </div>
        </aside>
        <main class="area">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>

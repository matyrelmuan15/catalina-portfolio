<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Panel' }} · {{ config('app.name') }}</title>
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
                <div class="marca">Catalina</div>
                <div class="sub">Administración</div>
            </div>
            <nav class="menu">
                <a href="{{ route('panel.videos') }}" class="{{ request()->routeIs('panel.videos*') ? 'activo' : '' }}">Videos</a>
                <a href="{{ route('panel.clientes') }}" class="{{ request()->routeIs('panel.clientes*') ? 'activo' : '' }}">Clientes</a>
                <a href="{{ route('panel.pedidos') }}" class="{{ request()->routeIs('panel.pedidos*') ? 'activo' : '' }}">Pedidos</a>
                <a href="{{ route('panel.cuenta') }}" class="{{ request()->routeIs('panel.cuenta*') ? 'activo' : '' }}">Cuenta</a>
            </nav>
            <div class="abajo">
                <a href="{{ route('portada') }}">← Ver el sitio</a>
                <form method="POST" action="{{ route('salir') }}">
                    @csrf
                    <button type="submit">Cerrar sesión</button>
                </form>
            </div>
        </aside>
        <main class="area">
            @if (session('estado'))
                <div class="pista" style="color:var(--tinta);border:1px solid var(--linea);padding:14px 18px;margin-bottom:24px">{{ session('estado') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>

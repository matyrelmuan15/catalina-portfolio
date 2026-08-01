<!DOCTYPE html>
<html lang="es">
<head>
    @include('partials.cabecera-html', ['titulo' => ($titulo ?? 'Portal de cliente').' · Catalina Avendaño'])
</head>
<body class="antialiased">
    <div class="armazon">
        <aside class="lateral">
            <div>
                <div class="marca">{{ auth()->user()?->cliente?->marca ?? 'Cliente' }}</div>
                <div class="sub">Portal de cliente</div>
            </div>
            <nav class="menu">
                <a href="{{ route('portal.calendario') }}" @class(['activo' => request()->routeIs('portal.calendario*')])>Calendario</a>
                <a href="{{ route('portal.publicaciones') }}" @class(['activo' => request()->routeIs('portal.publicaciones*')])>Publicaciones</a>
                <a href="{{ route('portal.pedidos') }}" @class(['activo' => request()->routeIs('portal.pedidos*')])>Mis pedidos</a>
                <a href="{{ route('portal.metricas') }}" @class(['activo' => request()->routeIs('portal.metricas*')])>Métricas</a>
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

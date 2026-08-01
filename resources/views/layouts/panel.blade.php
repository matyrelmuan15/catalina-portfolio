<!DOCTYPE html>
<html lang="es">
<head>
    @include('partials.cabecera-html', ['titulo' => ($titulo ?? 'Panel').' · Catalina Avendaño'])
</head>
<body class="antialiased">
    <div class="armazon">
        <aside class="lateral">
            <div>
                <div class="marca">Catalina</div>
                <div class="sub">Administración</div>
            </div>
            <nav class="menu">
                <a href="{{ route('panel.videos') }}" @class(['activo' => request()->routeIs('panel.videos*')])>Videos</a>
                <a href="{{ route('panel.clientes') }}" @class(['activo' => request()->routeIs('panel.clientes*')])>Clientes</a>
                <a href="{{ route('panel.pedidos') }}" @class(['activo' => request()->routeIs('panel.pedidos*')])>Pedidos</a>
                <a href="{{ route('panel.cuenta') }}" @class(['activo' => request()->routeIs('panel.cuenta*')])>Cuenta</a>
            </nav>
            <div class="abajo">
                <a href="{{ route('portfolio.inicio') }}">← Ver el sitio</a>
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

    <div class="aviso" x-data="{ mensaje: '', mostrar: false }"
         x-on:aviso.window="mensaje = $event.detail.mensaje; mostrar = true; setTimeout(() => mostrar = false, 2200)"
         x-show="mostrar" x-cloak x-text="mensaje"></div>

    @livewireScripts
</body>
</html>

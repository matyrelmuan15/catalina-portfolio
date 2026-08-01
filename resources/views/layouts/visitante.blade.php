<!DOCTYPE html>
<html lang="es">
<head>
    @include('partials.cabecera-html', ['titulo' => 'Ingresar · Catalina Avendaño'])
</head>
<body class="antialiased">
    <div class="pantalla-ingreso">
        <div class="tarjeta-ingreso">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>
</html>

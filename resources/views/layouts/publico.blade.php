<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $tituloPagina = $title ?? 'Catalina Avendaño · Contenido UGC, video y fotografía';
        $descripcionPagina = $description ?? 'Video vertical, fotografía y estrategia digital para marcas que quieren mostrarse cerca y vender mejor. Viedma, Río Negro.';
        $imagenPagina = $image ?? asset('img/og-portada.jpg');
    @endphp
    <title>{{ $tituloPagina }}</title>
    <meta name="description" content="{{ $descripcionPagina }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph (RF-06): que el enlace se vea bien al compartirlo. --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Catalina Avendaño">
    <meta property="og:locale" content="es_AR">
    <meta property="og:title" content="{{ $tituloPagina }}">
    <meta property="og:description" content="{{ $descripcionPagina }}">
    <meta property="og:image" content="{{ $imagenPagina }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $tituloPagina }}">
    <meta name="twitter:description" content="{{ $descripcionPagina }}">
    <meta name="twitter:image" content="{{ $imagenPagina }}">

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => 'Catalina Avendaño',
            'jobTitle' => 'Creadora de contenido y fotógrafa',
            'url' => url('/'),
            'image' => $imagenPagina,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Viedma',
                'addressRegion' => 'Río Negro',
                'addressCountry' => 'AR',
            ],
            'email' => 'catalinaavendanio@gmail.com',
            'sameAs' => ['https://instagram.com/kaatyavendanio'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;6..96,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>

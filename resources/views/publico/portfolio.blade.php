<?php
$columnaA = $videos->filter(fn ($v, $i) => $i % 2 === 0)->values();
$columnaB = $videos->filter(fn ($v, $i) => $i % 2 === 1)->values();
$imagenCompartir = $videos->map(fn ($v) => $miniaturas[$v->id] ?? null)->filter()->first();
$videosParaJs = $videos->mapWithKeys(fn ($v) => [$v->id => [
    'titulo' => $v->titulo,
    'categoria' => $v->categoria->value,
    'cliente' => $v->cliente_texto,
    'fecha' => $v->fecha->translatedFormat('d \d\e M \d\e Y'),
    'descripcion' => $v->descripcion,
    'embed' => $embebidos[$v->id],
]]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catalina Avendaño · Contenido UGC, video y fotografía</title>
    <meta name="description" content="Video vertical, fotografía y estrategia digital para marcas que quieren mostrarse cerca y vender mejor. Con base en Viedma, Río Negro.">
    <link rel="canonical" href="{{ route('portfolio.inicio') }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Catalina Avendaño">
    <meta property="og:title" content="Catalina Avendaño · Contenido UGC, video y fotografía">
    <meta property="og:description" content="Video vertical, fotografía y estrategia digital para marcas que quieren mostrarse cerca y vender mejor.">
    <meta property="og:url" content="{{ route('portfolio.inicio') }}">
    @if ($imagenCompartir)
        <meta property="og:image" content="{{ $imagenCompartir }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => 'Catalina Avendaño',
            'jobTitle' => 'Creadora de contenido, video y fotografía',
            'url' => route('portfolio.inicio'),
            'sameAs' => ['https://instagram.com/kaatyavendanio'],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Viedma',
                'addressRegion' => 'Río Negro',
                'addressCountry' => 'AR',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;6..96,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/publico.js'])
</head>
<body class="antialiased" x-data="{
        filtro: 'Todos',
        videoAbierto: null,
        videos: {{ Illuminate\Support\Js::from($videosParaJs) }},
        abrir(id) { this.videoAbierto = id },
        cerrar() { this.videoAbierto = null },
        hayEnCategoria(categoria) { return Object.values(this.videos).some(v => v.categoria === categoria) },
    }" @keydown.escape.window="cerrar()">

    <header class="barra">
        <div class="marca">Catalina Avendaño</div>
        <nav>
            <a href="#trabajos">Trabajos</a>
            <a href="#servicios">Servicios</a>
            <a href="#sobre">Sobre mí</a>
            <a href="#contacto">Contacto</a>
        </nav>
        <div class="acciones">
            <a class="enlace-sesion" href="{{ route('ingresar') }}">Iniciar sesión</a>
            <a class="boton" href="https://wa.me/542931403502" target="_blank" rel="noopener" style="padding:11px 20px">Escribime</a>
        </div>
    </header>

    <section id="hero">
        <div class="hero-grilla">
            <div class="hero-texto">
                <div class="rotulo">Contenido UGC · Viedma, Río Negro</div>
                <h1>Marcas que<br>se ven <em>reales</em></h1>
                <p>Video vertical, fotografía y estrategia digital para marcas que quieren mostrarse cerca y vender mejor.</p>
                <div class="hero-ctas">
                    <a class="boton" href="#trabajos">Ver trabajos</a>
                    <a class="boton fantasma" href="#contacto">Escribime</a>
                </div>
            </div>
            @if ($videos->isNotEmpty())
                <div class="tira">
                    @foreach (['baja' => $columnaA, 'sube' => $columnaB] as $direccion => $columna)
                        @php($piezas = $columna->isEmpty() ? $columnaA : $columna)
                        <div class="columna {{ $direccion }}">
                            @for ($vuelta = 0; $vuelta < 2; $vuelta++)
                                @foreach ($piezas as $video)
                                    <div class="encuadre {{ $miniaturas[$video->id] ? '' : $video->tonoClase() }}">
                                        @if ($miniaturas[$video->id])
                                            <img src="{{ $miniaturas[$video->id] }}" alt="" loading="lazy">
                                        @endif
                                        <span class="glifo">{{ $video->cliente_texto }}</span>
                                    </div>
                                @endforeach
                            @endfor
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section id="trabajos" class="seccion">
        <div class="contenedor">
            <div class="cabecera-seccion">
                <div>
                    <div class="rotulo tenue">Selección</div>
                    <h2>Trabajos</h2>
                </div>
                <p>Piezas verticales pensadas para Reels, TikTok y campañas pagas.</p>
            </div>

            <div class="filtros">
                <button class="filtro" :class="{ activo: filtro === 'Todos' }" @click="filtro = 'Todos'">Todos</button>
                @foreach ($categorias as $categoria)
                    <button class="filtro" :class="{ activo: filtro === '{{ $categoria->value }}' }" @click="filtro = '{{ $categoria->value }}'">{{ $categoria->value }}</button>
                @endforeach
            </div>

            <div class="grilla-trabajos">
                @forelse ($videos as $video)
                    <article class="trabajo"
                        x-show="filtro === 'Todos' || filtro === '{{ $video->categoria->value }}'"
                        tabindex="0"
                        @click="abrir({{ $video->id }})"
                        @keydown.enter="abrir({{ $video->id }})">
                        <div class="lienzo {{ $miniaturas[$video->id] ? '' : $video->tonoClase() }}">
                            @if ($miniaturas[$video->id])
                                <img src="{{ $miniaturas[$video->id] }}" alt="" loading="lazy">
                            @endif
                        </div>
                        <div class="velo"></div>
                        @if ($video->destacado)
                            <div class="marca-destacado">Destacado</div>
                        @endif
                        <div class="reproducir">
                            <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                        </div>
                        <div class="pie">
                            <div class="cliente">{{ $video->cliente_texto }} · {{ $video->categoria->value }}</div>
                            <div class="titulo">{{ $video->titulo }}</div>
                        </div>
                    </article>
                @empty
                    <div class="vacio-galeria">Todavía no hay trabajos publicados.</div>
                @endforelse

                <div class="vacio-galeria" x-show="filtro !== 'Todos' && !hayEnCategoria(filtro)" x-cloak>
                    Todavía no hay trabajos publicados en esta categoría.
                </div>
            </div>
        </div>
    </section>

    <section id="servicios" class="seccion">
        <div class="contenedor">
            <div class="cabecera-seccion">
                <div>
                    <div class="rotulo tenue">Qué hago</div>
                    <h2>Servicios</h2>
                </div>
                <p>Podés contratarme para una pieza suelta o para todo el ciclo: idea, producción y pauta.</p>
            </div>
            <div class="grupos">
                <div class="grupo">
                    <h3>Frente a cámara</h3>
                    <ul>
                        <li>Videos UGC</li>
                        <li>Videos lifestyle</li>
                        <li>Modelaje</li>
                    </ul>
                </div>
                <div class="grupo">
                    <h3>Producción</h3>
                    <ul>
                        <li>Videos de marketing</li>
                        <li>Grabación y edición</li>
                        <li>Fotografía</li>
                        <li>Diseño gráfico</li>
                    </ul>
                </div>
                <div class="grupo">
                    <h3>Crecimiento</h3>
                    <ul>
                        <li>Manejo de redes</li>
                        <li>Meta Ads</li>
                        <li>Google Ads</li>
                        <li>Sitios web a medida</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Retrato y cifras: contenido de referencia hasta contar con las fotos y los
         textos reales (pendiente #4 de AVANCE.md). --}}
    <section id="sobre" class="seccion">
        <div class="contenedor sobre">
            <div class="retrato"><span>Foto de perfil</span></div>
            <div>
                <div class="rotulo tenue">Sobre mí</div>
                <h2>Hago contenido que no parece publicidad.</h2>
                <p>Trabajo desde Viedma con marcas de indumentaria, gastronomía, belleza y servicios. Grabo, edito y publico: entrego el video listo para subir, con los formatos y las medidas que cada red necesita.</p>
                <p>Si además querés que el contenido trabaje en pauta, me ocupo de las campañas en Meta y Google, y del calendario de publicaciones.</p>
                <div class="datos">
                    <div class="dato"><div class="n">48</div><div class="r">Marcas</div></div>
                    <div class="dato"><div class="n">320</div><div class="r">Videos entregados</div></div>
                    <div class="dato"><div class="n">5</div><div class="r">Años de trabajo</div></div>
                </div>
            </div>
        </div>
    </section>

    <section id="contacto" class="seccion">
        <div class="contenedor">
            <div class="rotulo">Siguiente paso</div>
            <h2>Contame qué necesitás y te paso una <em>propuesta</em> en 48 horas.</h2>
            <a class="boton claro" href="https://wa.me/542931403502" target="_blank" rel="noopener">Escribir por WhatsApp</a>
            <div class="vias">
                <a class="via" href="https://wa.me/542931403502" target="_blank" rel="noopener">
                    <div class="r">WhatsApp</div><div class="v">2931 40-3502</div>
                </a>
                <a class="via" href="mailto:catalinaavendanio@gmail.com">
                    <div class="r">Correo</div><div class="v">catalinaavendanio@gmail.com</div>
                </a>
                <a class="via" href="https://instagram.com/kaatyavendanio" target="_blank" rel="noopener">
                    <div class="r">Instagram</div><div class="v">@kaatyavendanio</div>
                </a>
                <div class="via"><div class="r">Dónde estoy</div><div class="v">Viedma, Río Negro</div></div>
            </div>
        </div>
    </section>

    <footer>
        <span>© {{ now()->year }} Catalina Avendaño · Viedma, Río Negro</span>
        <span>catalinaavendanio@gmail.com</span>
    </footer>

    <template x-if="videoAbierto">
        <div class="telon" @click="if ($event.target === $el) cerrar()">
            <div class="visor">
                <button class="cerrar" @click="cerrar()" aria-label="Cerrar">✕</button>
                <div class="marco">
                    <template x-if="videoAbierto">
                        <iframe :src="videos[videoAbierto].embed" title="Reproductor de video" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                    </template>
                </div>
                <div class="info">
                    <div class="rotulo" x-text="videoAbierto ? videos[videoAbierto].categoria : ''"></div>
                    <h3 x-text="videoAbierto ? videos[videoAbierto].titulo : ''"></h3>
                    <p x-text="videoAbierto ? videos[videoAbierto].descripcion : ''"></p>
                    <div class="meta">
                        <div><div class="r">Cliente</div><div class="v" x-text="videoAbierto ? videos[videoAbierto].cliente : ''"></div></div>
                        <div><div class="r">Fecha</div><div class="v" x-text="videoAbierto ? videos[videoAbierto].fecha : ''"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</body>
</html>

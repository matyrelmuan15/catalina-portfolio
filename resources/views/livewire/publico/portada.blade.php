<div id="sitio" x-data="{
        categoriaActiva: 'Todas',
        videoAbierto: null,
        abrir(video) { this.videoAbierto = video },
        cerrar() { this.videoAbierto = null },
    }"
    @keydown.escape.window="cerrar()"
>
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
            <div class="tira" aria-hidden="true">
                <div class="columna baja">
                    @foreach ($videos->take(4) as $pieza)
                        <div class="encuadre" style="background-color:var(--rosa-humo)">
                            <span class="glifo">{{ $pieza->categoria }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="columna sube">
                    @foreach ($videos->skip(4)->take(4) as $pieza)
                        <div class="encuadre" style="background-color:var(--rosa-humo)">
                            <span class="glifo">{{ $pieza->categoria }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="trabajos">
        <div class="contenedor">
            <div class="cabecera-seccion">
                <div>
                    <div class="rotulo tenue">Selección</div>
                    <h2>Trabajos</h2>
                </div>
                <p>Piezas verticales pensadas para Reels, TikTok y campañas pagas.</p>
            </div>

            <div class="filtros">
                <button type="button" class="filtro" :class="categoriaActiva === 'Todas' && 'activo'" @click="categoriaActiva = 'Todas'">Todas</button>
                @foreach ($categorias as $categoria)
                    <button type="button" class="filtro" :class="categoriaActiva === '{{ $categoria }}' && 'activo'" @click="categoriaActiva = '{{ $categoria }}'">{{ $categoria }}</button>
                @endforeach
            </div>

            <div class="grilla-trabajos">
                @forelse ($videos as $pieza)
                    <div class="trabajo"
                        x-show="categoriaActiva === 'Todas' || categoriaActiva === '{{ $categoria = $pieza->categoria }}'"
                        @click="abrir({
                            titulo: @js($pieza->titulo),
                            cliente: @js($pieza->cliente_texto),
                            fecha: @js($pieza->fecha->translatedFormat('d \d\e M \d\e Y')),
                            descripcion: @js($pieza->descripcion),
                            categoria: @js($pieza->categoria),
                            embebida: @js($pieza->urlEmbebida()),
                        })"
                        style="background-color:var(--rosa-humo)"
                    >
                        @if ($pieza->destacado)
                            <span class="marca-destacado">Destacado</span>
                        @endif
                        <div class="velo"></div>
                        <div class="pie">
                            <div class="cliente">{{ $pieza->cliente_texto }}</div>
                            <div class="titulo">{{ $pieza->titulo }}</div>
                        </div>
                        <div class="reproducir">
                            <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                @empty
                    <div class="vacio-galeria">Todavía no hay trabajos publicados.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="servicios">
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

    <section id="sobre">
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

    <section id="contacto">
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

    <div class="telon" x-show="videoAbierto" x-cloak @click.self="cerrar()" style="display:none">
        <div class="visor" x-show="videoAbierto" x-transition>
            <button type="button" class="cerrar" @click="cerrar()" aria-label="Cerrar">×</button>
            <template x-if="videoAbierto">
                <div class="marco">
                    <iframe :src="videoAbierto.embebida" title="Reproductor de video" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                </div>
            </template>
            <div class="info" x-show="videoAbierto">
                <div class="rotulo tenue" x-text="videoAbierto && videoAbierto.categoria"></div>
                <h3 x-text="videoAbierto && videoAbierto.titulo"></h3>
                <p x-text="videoAbierto && videoAbierto.descripcion"></p>
                <div class="meta">
                    <div><div class="r">Cliente</div><div class="v" x-text="videoAbierto && videoAbierto.cliente"></div></div>
                    <div><div class="r">Fecha</div><div class="v" x-text="videoAbierto && videoAbierto.fecha"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

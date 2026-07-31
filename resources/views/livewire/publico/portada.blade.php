<div id="sitio">
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
        </div>
    </section>

    <footer>
        <span>© {{ now()->year }} Catalina Avendaño · Viedma, Río Negro</span>
        <span>catalinaavendanio@gmail.com</span>
    </footer>
</div>

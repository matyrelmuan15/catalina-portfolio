<div>
    <div class="encabezado-panel">
        <div>
            <h1>Videos</h1>
            <p>Lo que está publicado acá es lo que se ve en el portfolio.</p>
        </div>
        <button class="boton" wire:click="nuevo">+ Subir video</button>
    </div>

    <div class="metricas">
        <div class="metrica"><div class="n">{{ $totalCargados }}</div><div class="r">Cargados</div></div>
        <div class="metrica destacada"><div class="n">{{ $totalPublicados }}</div><div class="r">Publicados</div></div>
        <div class="metrica"><div class="n">{{ $totalOcultos }}</div><div class="r">Ocultos</div></div>
        <div class="metrica"><div class="n">{{ $totalDestacados }}</div><div class="r">Destacados</div></div>
    </div>

    <div class="herramientas">
        <input type="search" wire:model.live.debounce.300ms="busqueda" placeholder="Buscar por título o cliente">
        <select wire:model.live="categoriaFiltro">
            <option>Todas las categorías</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->value }}">{{ $categoria->value }}</option>
            @endforeach
        </select>
    </div>

    <div class="tabla-envoltorio">
        <table>
            <thead>
                <tr>
                    <th style="width:38%">Video</th>
                    <th>Categoría</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($videos as $video)
                    <tr wire:key="video-{{ $video->id }}">
                        <td>
                            <div class="celda-video">
                                @if ($miniaturas[$video->id])
                                    <img class="mini" src="{{ $miniaturas[$video->id] }}" alt="">
                                @else
                                    <div class="mini {{ $video->tonoClase() }}"></div>
                                @endif
                                <div>
                                    <div class="t">{{ $video->titulo }}</div>
                                    <div class="c">{{ $video->cliente_texto }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="etiqueta">{{ $video->categoria->value }}</span></td>
                        <td>{{ $video->fecha->translatedFormat('d M Y') }}</td>
                        <td>
                            <span class="estado">
                                <span class="punto {{ $video->publicado ? 'vivo' : '' }}"></span>
                                {{ $video->publicado ? 'En la web' : 'Oculto' }}
                            </span>
                        </td>
                        <td>
                            <div class="acciones-fila">
                                <button class="icono estrella {{ $video->destacado ? 'on' : '' }}" title="Destacar" wire:click="alternarDestacado({{ $video->id }})">★</button>
                                <button class="icono" title="{{ $video->publicado ? 'Ocultar' : 'Publicar' }}" wire:click="alternarPublicado({{ $video->id }})">{{ $video->publicado ? '◉' : '○' }}</button>
                                <button class="icono" title="Editar" wire:click="editar({{ $video->id }})">✎</button>
                                <button class="icono" title="Eliminar" wire:click="confirmarEliminar({{ $video->id }})">✕</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($videos->isEmpty())
        <div class="sin-datos">No hay videos con ese criterio.</div>
    @endif

    {{-- Alta y edición --}}
    @if ($formularioAbierto)
        <div class="telon" wire:click.self="cancelar">
            <div class="hoja">
                <button class="cerrar" wire:click="cancelar" aria-label="Cerrar">✕</button>
                <h3>{{ $editandoId ? 'Editar video' : 'Subir video' }}</h3>
                <p class="guia">{{ $editandoId ? 'Los cambios se ven en el sitio al guardar.' : 'Pegá el enlace del video y completá los datos.' }}</p>

                <form wire:submit="guardar">
                    <div class="fila">
                        <div class="campo">
                            <span>Título</span>
                            <input type="text" wire:model="titulo">
                            @error('titulo') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="campo">
                            <span>Cliente</span>
                            <input type="text" wire:model="clienteTexto">
                            @error('clienteTexto') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="fila">
                        <div class="campo">
                            <span>Categoría</span>
                            <select wire:model="categoria">
                                @foreach ($categorias as $opcion)
                                    <option value="{{ $opcion->value }}">{{ $opcion->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="campo">
                            <span>Fecha</span>
                            <input type="date" wire:model="fecha">
                            @error('fecha') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="campo">
                        <span>Enlace del video (YouTube, Vimeo o archivo)</span>
                        <input type="text" wire:model="enlace" placeholder="https://">
                        @error('enlace') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="campo">
                        <span>Descripción</span>
                        <textarea wire:model="descripcion" rows="3" maxlength="500"></textarea>
                        @error('descripcion') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="campo">
                        <span>Miniatura (opcional; si no subís una, se usa la del proveedor cuando se puede)</span>
                        <input type="file" wire:model="miniatura" accept="image/*">
                        <div wire:loading wire:target="miniatura" style="font-size:12px;color:var(--color-malva);margin-top:6px">Subiendo…</div>
                        @error('miniatura') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="interruptores">
                        <label class="interruptor">
                            <input type="checkbox" wire:model="publicado"> Mostrar en el sitio
                        </label>
                        <label class="interruptor">
                            <input type="checkbox" wire:model="destacado"> Destacar arriba de todo
                        </label>
                    </div>
                    <div class="pie-hoja">
                        <button type="button" class="boton fantasma" wire:click="cancelar">Cancelar</button>
                        <button type="submit" class="boton" wire:loading.attr="disabled" wire:target="guardar,miniatura">
                            {{ $editandoId ? 'Guardar cambios' : 'Subir video' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Confirmación de baja --}}
    @if ($confirmandoEliminarId)
        <div class="telon" wire:click.self="cancelarEliminar">
            <div class="hoja" style="max-width:430px">
                <h3>Eliminar video</h3>
                <p class="guia">Se borra del panel y del sitio. No se puede deshacer.</p>
                <div class="pie-hoja">
                    <button class="boton fantasma" wire:click="cancelarEliminar">Cancelar</button>
                    <button class="boton" wire:click="eliminar">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>

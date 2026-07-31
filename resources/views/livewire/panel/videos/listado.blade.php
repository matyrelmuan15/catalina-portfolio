<div>
    <div class="encabezado-panel">
        <div>
            <div class="rotulo tenue">Panel</div>
            <h1>Videos</h1>
            <p>Lo que cargués acá se refleja en el sitio público de inmediato.</p>
        </div>
        <button type="button" class="boton" wire:click="abrirAlta">Cargar video</button>
    </div>

    <div class="metricas">
        <div class="metrica">
            <div class="n">{{ $contadores['cargados'] }}</div>
            <div class="r">Cargados</div>
        </div>
        <div class="metrica destacada">
            <div class="n">{{ $contadores['publicados'] }}</div>
            <div class="r">Publicados</div>
        </div>
        <div class="metrica">
            <div class="n">{{ $contadores['ocultos'] }}</div>
            <div class="r">Ocultos</div>
        </div>
        <div class="metrica">
            <div class="n">{{ $contadores['destacados'] }}</div>
            <div class="r">Destacados</div>
        </div>
    </div>

    <div class="herramientas">
        <input type="search" wire:model.live.debounce.300ms="busqueda" placeholder="Buscar por título o cliente">
        <select wire:model.live="categoriaFiltro">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria }}">{{ $categoria }}</option>
            @endforeach
        </select>
        <div class="espacio"></div>
    </div>

    <div class="tabla-envoltorio">
        <table>
            <thead>
                <tr>
                    <th>Video</th>
                    <th>Categoría</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($videos as $video)
                    <tr wire:key="video-{{ $video->id }}">
                        <td>
                            <div class="celda-video">
                                <div class="mini" style="background-color:var(--rosa-humo)"></div>
                                <div>
                                    <div class="t">{{ $video->titulo }}</div>
                                    <div class="c">{{ $video->cliente_texto }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="etiqueta">{{ $video->categoria }}</span></td>
                        <td>{{ $video->fecha->translatedFormat('d M Y') }}</td>
                        <td>
                            <div class="estado">
                                <span class="punto {{ $video->publicado ? 'vivo' : '' }}"></span>
                                {{ $video->publicado ? 'Publicado' : 'Oculto' }}
                            </div>
                        </td>
                        <td>
                            <div class="acciones-fila">
                                <button type="button" class="icono estrella {{ $video->destacado ? 'on' : '' }}" wire:click="alternarDestacado({{ $video->id }})" title="Destacar" aria-label="Destacar">★</button>
                                <button type="button" class="icono" wire:click="alternarPublicado({{ $video->id }})" title="{{ $video->publicado ? 'Ocultar' : 'Publicar' }}" aria-label="Publicar u ocultar">{{ $video->publicado ? '⊘' : '✓' }}</button>
                                <button type="button" class="icono" wire:click="abrirEdicion({{ $video->id }})" title="Editar" aria-label="Editar">✎</button>
                                <button type="button" class="icono" wire:click="eliminar({{ $video->id }})" wire:confirm="¿Dar de baja este video? La acción es definitiva." title="Eliminar" aria-label="Eliminar">✕</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="sin-datos">No hay videos que coincidan con la búsqueda.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($modalAbierto)
        <div class="telon" wire:click.self="cerrarModal">
            <div class="hoja">
                <button type="button" class="cerrar" wire:click="cerrarModal" aria-label="Cerrar">×</button>
                @livewire('panel.videos.formulario', ['videoId' => $editandoId], key('formulario-'.($editandoId ?? 'nuevo')))
            </div>
        </div>
    @endif
</div>

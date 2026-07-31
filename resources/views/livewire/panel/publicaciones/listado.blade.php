<div>
    <div class="encabezado-panel">
        <div>
            <div class="rotulo tenue">Panel</div>
            <h1>Publicaciones{{ $cliente ? ' · '.$cliente->marca : '' }}</h1>
            <p>Registro de piezas publicadas o por publicar, con su rendimiento.</p>
        </div>
        <button type="button" class="boton" wire:click="abrirAlta">Nueva publicación</button>
    </div>

    <div class="herramientas">
        <input type="search" wire:model.live.debounce.300ms="busqueda" placeholder="Buscar por título">
        <select wire:model.live="estadoFiltro">
            <option value="">Todos los estados</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado }}">{{ $estado }}</option>
            @endforeach
        </select>
        <div class="espacio"></div>
    </div>

    <div class="tabla-ancha">
        <table class="ancha">
            <thead>
                <tr>
                    <th>Publicación</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Plataforma</th>
                    <th>Formato</th>
                    <th>Pilar</th>
                    @unless ($cliente)
                        <th>Cliente</th>
                    @endunless
                    <th class="num">Alcance</th>
                    <th class="num">Vistas</th>
                    <th class="num">Interacciones</th>
                    <th class="num">Me gusta</th>
                    <th class="num">Comentarios</th>
                    <th class="num">Compartidos</th>
                    <th class="num">Guardados</th>
                    <th class="num">Clics al enlace</th>
                    <th class="num">Tasa de interacción</th>
                    <th class="num">Seguidores al publicar</th>
                    <th>Medido el</th>
                    <th>Copy</th>
                    <th>Hashtags</th>
                    <th>Permalink</th>
                    <th>ID Media</th>
                    <th>Archivo final</th>
                    <th>Creativo en Figma</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($publicaciones as $publicacion)
                    @php $metrica = $publicacion->ultimaMetrica; @endphp
                    <tr wire:key="publicacion-{{ $publicacion->id }}">
                        <td><a href="{{ route('panel.publicaciones.ficha', $publicacion) }}" wire:navigate>{{ $publicacion->titulo }}</a></td>
                        <td><span class="insignia {{ $publicacion->claseEstado() }}">{{ $publicacion->estado }}</span></td>
                        <td>{{ $publicacion->fecha->format('d M Y') }}</td>
                        <td>{{ ucfirst($publicacion->plataforma) }}</td>
                        <td>{{ ucfirst($publicacion->formato) }}</td>
                        <td>{{ $publicacion->pilar ?? '—' }}</td>
                        @unless ($cliente)
                            <td>{{ $publicacion->cliente->marca }}</td>
                        @endunless
                        <td class="num">{{ $metrica?->alcance ?? '—' }}</td>
                        <td class="num">{{ $metrica?->vistas ?? '—' }}</td>
                        <td class="num">{{ $metrica?->interacciones ?? '—' }}</td>
                        <td class="num">{{ $metrica?->me_gusta ?? '—' }}</td>
                        <td class="num">{{ $metrica?->comentarios ?? '—' }}</td>
                        <td class="num">{{ $metrica?->compartidos ?? '—' }}</td>
                        <td class="num">{{ $metrica?->guardados ?? '—' }}</td>
                        <td class="num">{{ $metrica?->clics_enlace ?? '—' }}</td>
                        <td class="num">{{ $metrica && $metrica->tasa_interaccion !== null ? number_format((float) $metrica->tasa_interaccion, 1, ',', '.').'%' : '—' }}</td>
                        <td class="num">{{ $metrica?->seguidores_al_publicar ?? '—' }}</td>
                        <td>{{ $metrica?->medido_el?->format('d M Y') ?? '—' }}</td>
                        <td>{{ $publicacion->copy_texto ?? '—' }}</td>
                        <td>{{ $publicacion->hashtags ?? '—' }}</td>
                        <td>@if ($publicacion->permalink)<a class="enlace-mini" href="{{ $publicacion->permalink }}" target="_blank" rel="noopener">Ver</a>@else — @endif</td>
                        <td>{{ $publicacion->id_media ?? '—' }}</td>
                        <td>@if ($publicacion->archivo_final_url)<a class="enlace-mini" href="{{ $publicacion->archivo_final_url }}" target="_blank" rel="noopener">Ver</a>@else — @endif</td>
                        <td>@if ($publicacion->creativo_figma_url)<a class="enlace-mini" href="{{ $publicacion->creativo_figma_url }}" target="_blank" rel="noopener">Ver</a>@else — @endif</td>
                        <td>
                            <div class="acciones-fila">
                                <button type="button" class="icono" wire:click="abrirEdicion({{ $publicacion->id }})" title="Editar" aria-label="Editar">✎</button>
                                <button type="button" class="icono" wire:click="eliminar({{ $publicacion->id }})" wire:confirm="¿Eliminar esta publicación?" title="Eliminar" aria-label="Eliminar">✕</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="24"><div class="sin-datos">No hay publicaciones que coincidan.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($modalAbierto)
        <div class="telon" wire:click.self="cerrarModal">
            <div class="hoja">
                <button type="button" class="cerrar" wire:click="cerrarModal" aria-label="Cerrar">×</button>
                @livewire('panel.publicaciones.formulario', ['publicacionId' => $editandoId, 'cliente' => $cliente], key('formulario-publicacion-'.($editandoId ?? 'nuevo').'-'.($cliente?->id ?? '0')))
            </div>
        </div>
    @endif
</div>

<div>
    <div class="encabezado-panel">
        <div>
            <div class="rotulo tenue">Portal</div>
            <h1>Publicaciones</h1>
            <p>El resultado de lo que se publicó o se va a publicar.</p>
        </div>
    </div>

    <div class="tabla-ancha">
        <table class="ancha">
            <thead>
                <tr>
                    <th>Publicación</th>
                    <th>Fecha</th>
                    <th>Plataforma</th>
                    <th>Formato</th>
                    <th>Pilar</th>
                    <th class="num">Alcance</th>
                    <th class="num">Vistas</th>
                    <th class="num">Interacciones</th>
                    <th class="num">Me gusta</th>
                    <th class="num">Comentarios</th>
                    <th class="num">Compartidos</th>
                    <th class="num">Guardados</th>
                    <th class="num">Tasa de interacción</th>
                    <th>Medido el</th>
                    <th>Permalink</th>
                    <th>Archivo final</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($publicaciones as $publicacion)
                    @php $metrica = $publicacion->ultimaMetrica; @endphp
                    <tr wire:key="publicacion-{{ $publicacion->id }}">
                        <td>{{ $publicacion->titulo }}</td>
                        <td>{{ $publicacion->fecha->format('d M Y') }}</td>
                        <td>{{ ucfirst($publicacion->plataforma) }}</td>
                        <td>{{ ucfirst($publicacion->formato) }}</td>
                        <td>{{ $publicacion->pilar ?? '—' }}</td>
                        <td class="num">{{ $metrica?->alcance ?? '—' }}</td>
                        <td class="num">{{ $metrica?->vistas ?? '—' }}</td>
                        <td class="num">{{ $metrica?->interacciones ?? '—' }}</td>
                        <td class="num">{{ $metrica?->me_gusta ?? '—' }}</td>
                        <td class="num">{{ $metrica?->comentarios ?? '—' }}</td>
                        <td class="num">{{ $metrica?->compartidos ?? '—' }}</td>
                        <td class="num">{{ $metrica?->guardados ?? '—' }}</td>
                        <td class="num">{{ $metrica && $metrica->tasa_interaccion !== null ? number_format((float) $metrica->tasa_interaccion, 1, ',', '.').'%' : '—' }}</td>
                        <td>{{ $metrica?->medido_el?->format('d M Y') ?? '—' }}</td>
                        <td>@if ($publicacion->permalink)<a class="enlace-mini" href="{{ $publicacion->permalink }}" target="_blank" rel="noopener">Ver</a>@else — @endif</td>
                        <td>@if ($publicacion->archivo_final_url)<a class="enlace-mini" href="{{ $publicacion->archivo_final_url }}" target="_blank" rel="noopener">Ver</a>@else — @endif</td>
                    </tr>
                @empty
                    <tr><td colspan="16"><div class="sin-datos">Todavía no hay publicaciones cargadas.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

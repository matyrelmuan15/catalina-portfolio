<div>
    <div class="migas">
        <a href="{{ route('panel.publicaciones') }}" wire:navigate>← Publicaciones</a>
    </div>

    <div class="encabezado-panel">
        <div>
            <div class="rotulo tenue">{{ $publicacion->cliente->marca }}</div>
            <h1>{{ $publicacion->titulo }}</h1>
            <p>
                <span class="insignia {{ $publicacion->claseEstado() }}">{{ $publicacion->estado }}</span>
                · {{ $publicacion->fecha->translatedFormat('d \d\e M \d\e Y') }}
                · {{ ucfirst($publicacion->plataforma) }}
                · {{ ucfirst($publicacion->formato) }}
            </p>
        </div>
    </div>

    <div class="fila" style="align-items:flex-start">
        <div>
            <h3 style="font-size:16px;margin-bottom:14px">Datos propios</h3>
            <div class="visor" style="display:block;border:1px solid var(--linea)">
                <div class="meta" style="padding:22px;border-top:none;flex-direction:column;gap:16px">
                    <div><div class="r">Pilar</div><div class="v">{{ $publicacion->pilar ?? '—' }}</div></div>
                    <div><div class="r">Archivo final</div><div class="v">{{ $publicacion->archivo_final_url ? '['.$publicacion->archivo_final_url.']' : '—' }}</div></div>
                    <div><div class="r">Creativo en Figma</div><div class="v">{{ $publicacion->creativo_figma_url ?? '—' }}</div></div>
                </div>
            </div>
        </div>
        <div>
            <h3 style="font-size:16px;margin-bottom:14px">Datos de Meta</h3>
            <div class="visor" style="display:block;border:1px solid var(--linea)">
                <div class="meta" style="padding:22px;border-top:none;flex-direction:column;gap:16px">
                    <div><div class="r">ID Media</div><div class="v">{{ $publicacion->id_media ?? '—' }}</div></div>
                    <div><div class="r">Permalink</div><div class="v">{{ $publicacion->permalink ?? '—' }}</div></div>
                    <div><div class="r">Copy</div><div class="v">{{ $publicacion->copy_texto ?? '—' }}</div></div>
                    <div><div class="r">Hashtags</div><div class="v">{{ $publicacion->hashtags ?? '—' }}</div></div>
                </div>
            </div>
        </div>
    </div>

    <h3 style="font-size:16px;margin:30px 0 14px">Historial de mediciones</h3>
    <table>
        <thead>
            <tr>
                <th>Medido el</th>
                <th>Alcance</th>
                <th>Vistas</th>
                <th>Interacciones</th>
                <th>Me gusta</th>
                <th>Comentarios</th>
                <th>Tasa de interacción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($publicacion->metricas as $metrica)
                <tr>
                    <td>{{ $metrica->medido_el->format('d M Y') }}</td>
                    <td>{{ $metrica->alcance ?? '—' }}</td>
                    <td>{{ $metrica->vistas ?? '—' }}</td>
                    <td>{{ $metrica->interacciones ?? '—' }}</td>
                    <td>{{ $metrica->me_gusta ?? '—' }}</td>
                    <td>{{ $metrica->comentarios ?? '—' }}</td>
                    <td>{{ $metrica->tasa_interaccion !== null ? number_format((float) $metrica->tasa_interaccion, 1, ',', '.').'%' : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="sin-datos">Todavía no hay mediciones. Se cargan al importar (fase 7).</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

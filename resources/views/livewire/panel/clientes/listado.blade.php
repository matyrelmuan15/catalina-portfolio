<div>
    <div class="encabezado-panel">
        <div>
            <div class="rotulo tenue">Panel</div>
            <h1>Clientes</h1>
            <p>Cada marca activa entra a su portal con su propio acceso.</p>
        </div>
        <button type="button" class="boton" wire:click="abrirAlta">Nuevo cliente</button>
    </div>

    <div class="herramientas">
        <input type="search" wire:model.live.debounce.300ms="busqueda" placeholder="Buscar por marca o contacto">
        <label class="interruptor">
            <input type="checkbox" wire:model.live="verArchivados">
            Ver archivados
        </label>
        <div class="espacio"></div>
    </div>

    <div class="tabla-envoltorio">
        <table>
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Contacto</th>
                    <th>Próxima fecha</th>
                    <th>Pedidos abiertos</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientes as $cliente)
                    <tr wire:key="cliente-{{ $cliente->id }}">
                        <td>
                            <div class="celda-video">
                                <div class="inicial" style="background:var(--fucsia)">{{ mb_substr($cliente->marca, 0, 1) }}</div>
                                <div class="t">
                                    <a href="{{ route('panel.clientes.ficha', $cliente) }}" wire:navigate>{{ $cliente->marca }}</a>
                                </div>
                            </div>
                        </td>
                        <td>{{ $cliente->contacto }}</td>
                        <td>—</td>
                        <td>—</td>
                        <td>
                            <div class="estado">
                                <span class="punto {{ $cliente->archivado ? '' : 'vivo' }}"></span>
                                {{ $cliente->archivado ? 'Archivado' : 'Activo' }}
                            </div>
                        </td>
                        <td>
                            <div class="acciones-fila">
                                <a class="icono" href="{{ route('panel.clientes.ficha', $cliente) }}" wire:navigate title="Ver ficha" aria-label="Ver ficha">→</a>
                                @if ($cliente->archivado)
                                    <button type="button" class="icono" wire:click="desarchivar({{ $cliente->id }})" title="Desarchivar" aria-label="Desarchivar">↺</button>
                                @else
                                    <button type="button" class="icono" wire:click="archivar({{ $cliente->id }})" wire:confirm="¿Archivar a {{ $cliente->marca }}? Su usuario no va a poder iniciar sesión." title="Archivar" aria-label="Archivar">⊘</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="sin-datos">No hay clientes que coincidan con la búsqueda.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p style="font-size:11px;color:var(--malva);margin-top:10px">Próxima fecha y pedidos abiertos se completan en las fases 5 y 9.</p>

    @if ($modalAbierto)
        <div class="telon" wire:click.self="cerrarModal">
            <div class="hoja">
                <button type="button" class="cerrar" wire:click="cerrarModal" aria-label="Cerrar">×</button>
                @livewire('panel.clientes.formulario', [], key('formulario-cliente-'.now()->timestamp))
            </div>
        </div>
    @endif
</div>

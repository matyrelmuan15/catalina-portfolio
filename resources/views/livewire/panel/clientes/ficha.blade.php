<div>
    <div class="migas">
        <a href="{{ route('panel.clientes') }}" wire:navigate>← Clientes</a>
    </div>

    <div class="encabezado-panel">
        <div>
            <div class="rotulo tenue">{{ $cliente->archivado ? 'Cliente archivado' : 'Cliente activo' }}</div>
            <h1>{{ $cliente->marca }}</h1>
            <p>{{ $cliente->contacto }} · {{ $cliente->correo_contacto }}{{ $cliente->telefono ? ' · '.$cliente->telefono : '' }}</p>
        </div>
        <div class="acciones-fila">
            <button type="button" class="boton fantasma chico" wire:click="restablecerClave" wire:confirm="¿Generar una clave nueva para {{ $cliente->marca }}? La anterior deja de funcionar.">Restablecer clave</button>
            @if ($cliente->archivado)
                <button type="button" class="boton chico" wire:click="desarchivar">Desarchivar</button>
            @else
                <button type="button" class="boton peligro chico" wire:click="archivar" wire:confirm="¿Archivar a {{ $cliente->marca }}? Su usuario no va a poder iniciar sesión.">Archivar</button>
            @endif
        </div>
    </div>

    @if ($claveGenerada)
        <div class="pista" style="border:1px solid var(--linea);padding:18px;margin-bottom:24px">
            Clave nueva, comunicala por fuera del sistema. No se vuelve a mostrar: <code>{{ $claveGenerada }}</code>
        </div>
    @endif

    <div class="subpestanas">
        <button type="button" class="{{ $solapa === 'calendario' ? 'activo' : '' }}" wire:click="cambiarSolapa('calendario')">Calendario</button>
        <button type="button" class="{{ $solapa === 'publicaciones' ? 'activo' : '' }}" wire:click="cambiarSolapa('publicaciones')">Publicaciones</button>
        <button type="button" class="{{ $solapa === 'pedidos' ? 'activo' : '' }}" wire:click="cambiarSolapa('pedidos')">Pedidos</button>
        <button type="button" class="{{ $solapa === 'metricas' ? 'activo' : '' }}" wire:click="cambiarSolapa('metricas')">Métricas</button>
    </div>

    @if ($solapa === 'calendario')
        @livewire('panel.clientes.calendario', ['cliente' => $cliente], key('calendario-'.$cliente->id))
    @elseif ($solapa === 'publicaciones')
        @livewire('panel.publicaciones.listado', ['cliente' => $cliente], key('publicaciones-'.$cliente->id))
    @elseif ($solapa === 'pedidos')
        <div class="sin-datos">Los pedidos de {{ $cliente->marca }} se completan en la fase 9.</div>
    @else
        <div class="sin-datos">Las métricas de {{ $cliente->marca }} se completan en la fase 8.</div>
    @endif
</div>

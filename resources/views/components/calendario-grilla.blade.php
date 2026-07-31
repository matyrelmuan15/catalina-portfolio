@props(['mesActual', 'dias', 'proximas', 'editable' => false])

<div class="cal-cabecera">
    <h3>{{ ucfirst($mesActual->translatedFormat('F \d\e Y')) }}</h3>
    <div class="cal-nav">
        <button type="button" wire:click="mesAnterior" aria-label="Mes anterior">‹</button>
        <button type="button" wire:click="mesSiguiente" aria-label="Mes siguiente">›</button>
    </div>
    @if ($editable)
        <button type="button" class="boton chico" wire:click="abrirAlta">Nuevo evento</button>
    @endif
</div>

<div class="cal-envoltorio">
    <div class="cal-grilla">
        @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $nombreDia)
            <div class="cal-dia-nombre">{{ $nombreDia }}</div>
        @endforeach

        @foreach ($dias as $dia)
            <div class="cal-dia {{ $dia['otroMes'] ? 'otro' : '' }} {{ $dia['hoy'] ? 'hoy' : '' }}">
                <div class="num">{{ $dia['fecha']->day }}</div>
                @if ($editable)
                    <button type="button" class="agregar" wire:click="abrirAlta('{{ $dia['fecha']->toDateString() }}')" aria-label="Agregar evento">+</button>
                @endif
                @foreach ($dia['eventos'] as $evento)
                    <button
                        type="button"
                        class="evento tipo-{{ $evento->tipoCss() }}"
                        @if ($editable && $evento->esEditable()) wire:click="abrirEdicion({{ $evento->id }})" @endif
                        style="{{ $editable && $evento->esEditable() ? '' : 'cursor:default' }}"
                    >
                        <small>{{ $evento->etiquetaTipo() }}</small>
                        {{ $evento->titulo }}
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<div class="proximas">
    <h4>Próximas fechas</h4>
    @forelse ($proximas as $evento)
        <div class="proxima">
            <div class="f">{{ $evento->fecha->format('d/m') }}</div>
            <div>
                <div class="t">{{ $evento->titulo }}</div>
                <div class="d">{{ $evento->etiquetaTipo() }}</div>
            </div>
            @if ($editable)
                <div class="acc">
                    <button type="button" class="icono" wire:click="abrirEdicion({{ $evento->id }})" aria-label="Editar">✎</button>
                    <button type="button" class="icono" wire:click="eliminar({{ $evento->id }})" wire:confirm="¿Eliminar este evento?" aria-label="Eliminar">✕</button>
                </div>
            @endif
        </div>
    @empty
        <div class="sin-datos">No hay fechas próximas.</div>
    @endforelse
</div>

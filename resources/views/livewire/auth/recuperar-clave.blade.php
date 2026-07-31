<div class="tarjeta-ingreso">
    <div class="rotulo">Acceso</div>
    <div class="marca">Recuperar clave</div>
    <p class="intro">Te mandamos un enlace para elegir una clave nueva. Vence en 60 minutos.</p>

    @if ($mensaje)
        <div class="pista" style="border:none;padding:0;margin-bottom:24px">{{ $mensaje }}</div>
    @else
        <form wire:submit="enviar">
            <div class="campo">
                <span>Correo</span>
                <input type="email" wire:model="correo" placeholder="tucorreo@marca.com" autofocus>
                @error('correo') <div class="error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="boton ancho">Enviar enlace</button>
        </form>
    @endif

    <a class="volver" href="{{ route('ingresar') }}">← Volver a ingresar</a>
</div>

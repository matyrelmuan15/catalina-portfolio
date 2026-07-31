<div class="tarjeta-ingreso">
    <div class="rotulo">Acceso</div>
    <div class="marca">Elegir clave nueva</div>
    <p class="intro">Mínimo diez caracteres. Evitá claves que ya se filtraron en otros sitios.</p>

    <form wire:submit="restablecer">
        <div class="campo">
            <span>Correo</span>
            <input type="email" wire:model="correo" placeholder="tucorreo@marca.com">
            @error('correo') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="campo">
            <span>Clave nueva</span>
            <input type="password" wire:model="clave" placeholder="••••••••" autofocus>
            @error('clave') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="campo">
            <span>Repetir clave</span>
            <input type="password" wire:model="clave_confirmation" placeholder="••••••••">
        </div>
        <button type="submit" class="boton ancho">Guardar clave</button>
    </form>

    <a class="volver" href="{{ route('ingresar') }}">← Volver a ingresar</a>
</div>

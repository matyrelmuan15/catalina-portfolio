<div class="tarjeta-ingreso">
    <div class="rotulo">Acceso</div>
    <div class="marca">Segundo factor</div>
    <p class="intro">Ingresá el código de seis dígitos de tu aplicación de autenticación.</p>

    <form wire:submit="verificar">
        <div class="campo">
            <span>Código</span>
            <input type="text" inputmode="numeric" maxlength="6" wire:model="codigo" placeholder="000000" autofocus>
            @error('codigo') <div class="error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="boton ancho">Verificar</button>
    </form>

    <a class="volver" href="{{ route('ingresar') }}">← Volver a ingresar</a>
</div>

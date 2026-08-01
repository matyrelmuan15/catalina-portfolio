<div>
    <div class="rotulo">Acceso</div>
    <div class="marca">Recuperar clave</div>

    @if ($enviado)
        <p class="intro">Si el correo está registrado, te enviamos un enlace para restablecer la clave. Vence en 60 minutos.</p>
    @else
        <p class="intro">Escribí el correo con el que entrás al sistema.</p>

        <form wire:submit="enviar">
            <div class="campo">
                <span>Correo</span>
                <input wire:model="correo" type="email" placeholder="tucorreo@marca.com" autocomplete="username" required autofocus>
                @error('correo') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="boton ancho" wire:loading.attr="disabled" wire:target="enviar">Enviar enlace</button>
        </form>
    @endif

    <a class="volver" href="{{ route('ingresar') }}">← Volver al ingreso</a>
</div>

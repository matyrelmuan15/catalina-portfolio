<div>
    <div class="rotulo">Acceso</div>
    <div class="marca">Restablecer clave</div>

    @if ($listo)
        <p class="intro">Listo, tu clave se actualizó. Ya podés ingresar con la nueva.</p>
        <a class="boton ancho" href="{{ route('ingresar') }}" style="text-align:center">Ir a ingresar</a>
    @else
        <p class="intro">Elegí una clave nueva de al menos 10 caracteres.</p>

        <form wire:submit="restablecer">
            <div class="campo">
                <span>Correo</span>
                <input wire:model="correo" type="email" autocomplete="username" required>
                @error('correo') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <span>Clave nueva</span>
                <input wire:model="clave" type="password" autocomplete="new-password" required>
                @error('clave') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <span>Repetí la clave</span>
                <input wire:model="clave_confirmation" type="password" autocomplete="new-password" required>
            </div>

            <div class="error">{{ $error }}</div>

            <button type="submit" class="boton ancho" wire:loading.attr="disabled" wire:target="restablecer">Restablecer</button>
        </form>
    @endif

    <a class="volver" href="{{ route('ingresar') }}">← Volver al ingreso</a>
</div>

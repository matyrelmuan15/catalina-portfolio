<div>
    <div class="rotulo">Segundo factor</div>
    <div class="marca">Confirmá tu ingreso</div>
    <p class="intro">Abrí tu aplicación de autenticación y escribí el código de 6 dígitos.</p>

    <form wire:submit="confirmar">
        <div class="campo">
            <span>Código</span>
            <input wire:model="codigo" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000" required autofocus>
            @error('codigo') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="error">{{ $error }}</div>

        <button type="submit" class="boton ancho" wire:loading.attr="disabled" wire:target="confirmar">Confirmar</button>
    </form>

    <a class="volver" href="{{ route('ingresar') }}">← Volver al ingreso</a>
</div>

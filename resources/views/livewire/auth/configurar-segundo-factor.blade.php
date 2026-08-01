<div>
    <div class="rotulo">Segundo factor</div>
    <div class="marca">Activá la verificación en dos pasos</div>
    <p class="intro">Es obligatoria para la cuenta de administración. Escaneá el código con Google Authenticator, Authy o similar y confirmá con el código que te muestre.</p>

    <div style="background:#fff;padding:16px;margin-bottom:18px;max-width:220px">
        {!! $qrSvg !!}
    </div>

    <p class="intro" style="font-size:12px">Si no podés escanearlo, cargá esta clave manualmente: <code>{{ $secreto }}</code></p>

    <form wire:submit="confirmar">
        <div class="campo">
            <span>Código de confirmación</span>
            <input wire:model="codigo" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000" required>
            @error('codigo') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="error">{{ $error }}</div>

        <button type="submit" class="boton ancho" wire:loading.attr="disabled" wire:target="confirmar">Activar y entrar</button>
    </form>
</div>

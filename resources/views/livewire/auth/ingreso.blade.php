<div class="tarjeta-ingreso">
    <div class="rotulo">Acceso</div>
    <div class="marca">Iniciar sesión</div>
    <p class="intro">Entrá a tu calendario, tus pedidos y tus métricas.</p>

    <form wire:submit="ingresar">
        <div class="campo">
            <span>Correo</span>
            <input type="email" wire:model="correo" placeholder="tucorreo@marca.com" autocomplete="username" autofocus>
            @error('correo') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="campo">
            <span>Clave</span>
            <input type="password" wire:model="clave" placeholder="••••••••" autocomplete="current-password">
            @error('clave') <div class="error">{{ $message }}</div> @enderror
        </div>
        <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:rgba(255,255,255,.6)">
            <input type="checkbox" wire:model="recordarme" style="width:auto">
            Recordarme
        </label>
        <div class="error">@error('ingreso') {{ $message }} @enderror</div>
        <button type="submit" class="boton ancho" wire:loading.attr="disabled" wire:target="ingresar">Entrar</button>
    </form>

    <div class="pista">
        <a href="{{ route('clave.recuperar') }}" class="volver" style="margin:0;text-align:left">¿Olvidaste tu clave?</a>
    </div>
    <a class="volver" href="{{ route('portada') }}">← Volver al portfolio</a>
</div>

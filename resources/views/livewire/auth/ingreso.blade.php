<div>
    <div class="rotulo">Acceso</div>
    <div class="marca">Iniciar sesión</div>
    <p class="intro">Entrá a tu calendario, tus pedidos y tus métricas.</p>

    <form wire:submit="ingresar">
        <div class="campo">
            <span>Correo</span>
            <input wire:model="correo" type="email" placeholder="tucorreo@marca.com" autocomplete="username" required>
            @error('correo') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="campo">
            <span>Clave</span>
            <input wire:model="clave" type="password" placeholder="••••••••" autocomplete="current-password" required>
            @error('clave') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div wire:ignore>
            <div id="turnstile-ingreso"></div>
        </div>
        @error('turnstileToken') <div class="error">{{ $message }}</div> @enderror

        <div class="error">{{ $error }}</div>

        <button type="submit" class="boton ancho" wire:loading.attr="disabled" wire:target="ingresar">Entrar</button>
    </form>

    <a class="volver" href="{{ route('clave.recuperar') }}" style="margin-top:14px">¿Olvidaste tu clave?</a>
    <a class="volver" href="{{ route('portfolio.inicio') }}">← Volver al portfolio</a>

    @once
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endonce
    <script>
        document.addEventListener('livewire:initialized', () => {
            const objetivo = document.getElementById('turnstile-ingreso');
            const intentarRenderizar = () => {
                if (window.turnstile && objetivo && !objetivo.dataset.render) {
                    objetivo.dataset.render = '1';
                    window.turnstile.render(objetivo, {
                        sitekey: @js(config('services.turnstile.site_key')),
                        callback: (token) => @this.set('turnstileToken', token),
                    });
                } else if (!window.turnstile) {
                    setTimeout(intentarRenderizar, 150);
                }
            };
            intentarRenderizar();
        });
    </script>
</div>

<div>
    <div class="encabezado-panel">
        <div>
            <div class="rotulo tenue">Cuenta</div>
            <h1>Segundo factor</h1>
            <p>Es obligatorio para la administradora. Escaneá el código con Google Authenticator, Authy o similar.</p>
        </div>
    </div>

    @if (auth()->user()->tieneSegundoFactorActivo())
        <div class="pista" style="color:var(--tinta);border:1px solid var(--linea);padding:18px">El segundo factor ya está activo en tu cuenta.</div>
    @else
        <div class="fila" style="max-width:640px">
            <div>
                <img src="data:image/svg+xml;base64,{{ base64_encode((new \BaconQrCode\Writer(new \BaconQrCode\Renderer\ImageRenderer(new \BaconQrCode\Renderer\RendererStyle\RendererStyle(220), new \BaconQrCode\Renderer\Image\SvgImageBackEnd())))->writeString($this->urlQr())) }}" alt="Código QR del segundo factor" style="border:1px solid var(--linea)">
            </div>
            <div>
                <p style="color:var(--malva);font-size:13px;margin-bottom:16px">Si no podés escanear el código, cargá esta clave a mano: <code>{{ $secreto }}</code></p>
                <form wire:submit="activar">
                    <div class="campo">
                        <span>Código de verificación</span>
                        <input type="text" inputmode="numeric" maxlength="6" wire:model="codigo" placeholder="000000">
                        @error('codigo') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="boton">Activar segundo factor</button>
                </form>
            </div>
        </div>
    @endif
</div>

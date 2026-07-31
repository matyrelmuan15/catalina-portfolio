<div>
    @if ($claveGenerada)
        <h3>Cliente creado</h3>
        <p class="guia">Comunicale esta clave por fuera del sistema (WhatsApp, en persona). No se vuelve a mostrar.</p>
        <div class="pista" style="border:1px solid var(--linea);padding:18px;margin-bottom:20px">
            Correo: <code>{{ $correo }}</code><br>
            Clave inicial: <code>{{ $claveGenerada }}</code>
        </div>
        <div class="pie-hoja">
            <button type="button" class="boton" wire:click="$parent.cerrarModal()">Listo, ya la copié</button>
        </div>
    @else
        <h3>Nuevo cliente</h3>
        <p class="guia">Crea el cliente y su acceso al portal en un solo paso.</p>

        <form wire:submit="guardar">
            <div class="campo">
                <span>Marca *</span>
                <input type="text" wire:model="marca" maxlength="120">
                @error('marca') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="fila">
                <div class="campo">
                    <span>Persona de contacto *</span>
                    <input type="text" wire:model="contacto" maxlength="120">
                    @error('contacto') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="campo">
                    <span>Teléfono</span>
                    <input type="text" wire:model="telefono" maxlength="40">
                    @error('telefono') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="campo">
                <span>Correo de acceso al portal *</span>
                <input type="email" wire:model="correo" maxlength="180">
                @error('correo') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <span>Color de ficha</span>
                <div class="tonos">
                    @foreach (\App\Livewire\Panel\Clientes\Formulario::TONOS as $tono)
                        <button type="button" class="tono {{ $color === $tono ? 'sel' : '' }}" style="background:var(--{{ $tono === 'fucsia' ? 'fucsia' : ($tono === 'rosa' ? 'rosa-humo' : 'tinta') }})" wire:click="$set('color', '{{ $tono }}')" title="{{ $tono }}" aria-label="{{ $tono }}"></button>
                    @endforeach
                </div>
                @error('color') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="pie-hoja">
                <button type="button" class="boton fantasma" wire:click="$parent.cerrarModal()">Cancelar</button>
                <button type="submit" class="boton" wire:loading.attr="disabled" wire:target="guardar">Crear cliente</button>
            </div>
        </form>
    @endif
</div>

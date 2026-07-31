<div>
    <h3>{{ $publicacionId ? 'Editar publicación' : 'Nueva publicación' }}</h3>
    <p class="guia">Copy, hashtags, métricas, permalink e ID Media llegan con la importación (fase 7).</p>

    <form wire:submit="guardar">
        @unless ($clienteFijoId)
            <div class="campo">
                <span>Cliente *</span>
                <select wire:model="cliente_id">
                    <option value="">Elegir…</option>
                    @foreach ($clientes as $opcionCliente)
                        <option value="{{ $opcionCliente->id }}">{{ $opcionCliente->marca }}</option>
                    @endforeach
                </select>
                @error('cliente_id') <div class="error">{{ $message }}</div> @enderror
            </div>
        @endunless

        <div class="campo">
            <span>Título interno *</span>
            <input type="text" wire:model="titulo" maxlength="160">
            @error('titulo') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="fila">
            <div class="campo">
                <span>Fecha *</span>
                <input type="date" wire:model="fecha">
                @error('fecha') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <span>Estado *</span>
                <select wire:model="estado">
                    @foreach (\App\Models\Publicacion::ESTADOS_MANUALES as $estadoOpcion)
                        <option value="{{ $estadoOpcion }}">{{ $estadoOpcion }}</option>
                    @endforeach
                </select>
                @error('estado') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <span>Plataforma *</span>
                <select wire:model="plataforma">
                    <option value="instagram">Instagram</option>
                    <option value="facebook">Facebook</option>
                </select>
                @error('plataforma') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <span>Formato *</span>
                <select wire:model="formato">
                    <option value="reel">Reel</option>
                    <option value="carrusel">Carrusel</option>
                    <option value="imagen">Imagen</option>
                    <option value="historia">Historia</option>
                    <option value="video">Video</option>
                </select>
                @error('formato') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="campo">
            <span>Pilar de contenido</span>
            <input type="text" wire:model="pilar" maxlength="40" placeholder="Producto, educativo, testimonio…">
            @error('pilar') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="fila">
            <div class="campo">
                <span>Archivo final</span>
                <input type="url" wire:model="archivo_final_url" placeholder="https://drive.google.com/…">
                @error('archivo_final_url') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <span>Creativo en Figma</span>
                <input type="url" wire:model="creativo_figma_url" placeholder="https://figma.com/…">
                @error('creativo_figma_url') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="pie-hoja">
            <button type="button" class="boton fantasma" wire:click="$parent.cerrarModal()">Cancelar</button>
            <button type="submit" class="boton">Guardar</button>
        </div>
    </form>
</div>

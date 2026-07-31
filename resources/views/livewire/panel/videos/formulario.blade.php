<div>
    <h3>{{ $videoId ? 'Editar video' : 'Cargar video' }}</h3>
    <p class="guia">Los campos con * son obligatorios.</p>

    <form wire:submit="guardar">
        <div class="campo">
            <span>Título *</span>
            <input type="text" wire:model="titulo" maxlength="120">
            @error('titulo') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="fila">
            <div class="campo">
                <span>Cliente *</span>
                <input type="text" wire:model="cliente_texto" maxlength="120" placeholder="Texto libre, no hace falta que sea un cliente del portal">
                @error('cliente_texto') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <span>Categoría *</span>
                <select wire:model="categoria">
                    <option value="">Elegir…</option>
                    @foreach (\App\Models\Video::CATEGORIAS as $categoria)
                        <option value="{{ $categoria }}">{{ $categoria }}</option>
                    @endforeach
                </select>
                @error('categoria') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <span>Fecha del trabajo *</span>
                <input type="date" wire:model="fecha">
                @error('fecha') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <span>Enlace del video *</span>
                <input type="url" wire:model="enlace" placeholder="https://vimeo.com/… o https://youtube.com/…">
                @error('enlace') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="campo">
            <span>Descripción</span>
            <textarea wire:model="descripcion" rows="3" maxlength="500"></textarea>
            @error('descripcion') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="campo">
            <span>Miniatura (opcional, se recorta a 9:16 y se guarda en WebP)</span>
            <input type="file" wire:model="miniatura" accept="image/*">
            @error('miniatura') <div class="error">{{ $message }}</div> @enderror
            <div wire:loading wire:target="miniatura" style="font-size:12px;color:var(--malva);margin-top:8px">Procesando imagen…</div>
            @if ($miniatura)
                <img src="{{ $miniatura->temporaryUrl() }}" alt="Vista previa" style="width:90px;margin-top:10px;aspect-ratio:9/16;object-fit:cover">
            @elseif ($miniaturaActualUrl)
                <img src="{{ $miniaturaActualUrl }}" alt="Miniatura actual" style="width:90px;margin-top:10px;aspect-ratio:9/16;object-fit:cover">
            @else
                <p style="font-size:12px;color:var(--malva);margin-top:8px">Sin miniatura propia se usa la del proveedor cuando esté disponible.</p>
            @endif
        </div>

        <div class="interruptores">
            <label class="interruptor">
                <input type="checkbox" wire:model="publicado">
                Publicado
            </label>
            <label class="interruptor">
                <input type="checkbox" wire:model="destacado">
                Destacado
            </label>
        </div>

        <div class="pie-hoja">
            <button type="button" class="boton fantasma" wire:click="$parent.cerrarModal()">Cancelar</button>
            <button type="submit" class="boton" wire:loading.attr="disabled" wire:target="guardar">Guardar</button>
        </div>
    </form>
</div>

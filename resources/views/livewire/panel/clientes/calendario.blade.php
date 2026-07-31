<div>
    <x-calendario-grilla :mes-actual="$mesActual" :dias="$dias" :proximas="$proximas" :editable="true" />

    @if ($modalAbierto)
        <div class="telon" wire:click.self="cerrarModal">
            <div class="hoja">
                <button type="button" class="cerrar" wire:click="cerrarModal" aria-label="Cerrar">×</button>
                <h3>{{ $editandoId ? 'Editar evento' : 'Nuevo evento' }}</h3>
                <p class="guia">Solo vos podés cargar, editar o borrar fechas. {{ $cliente->marca }} las ve apenas se guardan.</p>

                <form wire:submit="guardar">
                    <div class="fila">
                        <div class="campo">
                            <span>Fecha *</span>
                            <input type="date" wire:model="fecha">
                            @error('fecha') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="campo">
                            <span>Tipo *</span>
                            <select wire:model="tipo">
                                <option value="grabacion">Grabación</option>
                                <option value="entrega">Entrega</option>
                                <option value="reunion">Reunión</option>
                            </select>
                            @error('tipo') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="campo">
                        <span>Título *</span>
                        <input type="text" wire:model="titulo" maxlength="160">
                        @error('titulo') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="campo">
                        <span>Nota visible para el cliente</span>
                        <textarea wire:model="nota" rows="3" maxlength="1000"></textarea>
                        @error('nota') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="pie-hoja">
                        <button type="button" class="boton fantasma" wire:click="cerrarModal">Cancelar</button>
                        <button type="submit" class="boton">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

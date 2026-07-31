<div>
    <div class="encabezado-panel">
        <div>
            <div class="rotulo tenue">Portal</div>
            <h1>Calendario</h1>
            <p>Las fechas que Catalina comprometió con {{ $marca }}.</p>
        </div>
    </div>

    <x-calendario-grilla :mes-actual="$mesActual" :dias="$dias" :proximas="$proximas" :editable="false" />
</div>

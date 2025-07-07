<div class="container-fluid vh-100 d-flex flex-column justify-content-center align-items-center">

    {{-- Step 1: Select Area --}}
    @if($step === 'selectArea')
        <h1 class="display-4 mb-3">Seleccione su Área</h1>

        {{-- 2×2 grid with tighter gutters, only the name --}}
        <div class="row w-100 gx-2 gy-2">
            @foreach($areas as $area)
                <div class="col-6">
                    <button 
                        wire:click="selectArea({{ $area->id }})"
                        class="btn btn-outline-primary btn-lg w-100 h-100 d-flex align-items-center justify-content-center"
                        style="min-height: 140px;"
                    >
                        <span class="fs-1">{{ $area->nombre_area }}</span>
                    </button>
                </div>
            @endforeach
        </div>

        @if($areas->hasPages())
            <div class="w-100 mt-2 d-flex justify-content-center">
                {{ $areas->links() }}
            </div>
        @endif
    @endif

    {{-- Step 2: Ticket Type --}}
    @if($step === 'selectType')
        <h2 class="mb-4">Área: {{ $selectedArea->nombre_area }}</h2>
        <div class="d-flex flex-column flex-md-row gap-3">
            <button wire:click="issueTicket(false)" class="btn btn-primary flex-fill py-3">
                Ticket Normal
            </button>
            <button wire:click="issueTicket(true)" class="btn btn-warning flex-fill py-3">
                Adulto Mayor
            </button>
        </div>
    @endif

    {{-- Step 3: Print Ticket --}}
    @if($step === 'printTicket')
        <div 
            class="text-center border rounded p-4 bg-white shadow"
            wire:poll.once.3000ms="resetToMenu"
        >
            <h2>Gobernación de Cochabamba</h2>
            <hr>

            {{-- Show the correct number --}}
            <p class="display-3 fw-bold">
                {{ $ticket->es_adulto_mayor ? $ticket->numero_adulto_mayor : $ticket->numero }}
            </p>

            <div class="mb-2">Área: <strong>{{ $selectedArea->nombre_area }}</strong></div>
            <div class="mb-2">Código: <strong>{{ $selectedArea->codigo_area }}</strong></div>
            <div class="mb-3"><em>{{ $ticket->es_adulto_mayor ? 'Adulto Mayor' : 'Normal' }}</em></div>
            <div class="text-muted mb-3">
                {{ $ticket->created_at->format('d/m/Y H:i') }}
            </div>

            {{-- auto-print --}}
            <script>window.print();</script>

            <div class="text-secondary">Regresando al inicio en 3 segundos…</div>
        </div>
    @endif


</div>

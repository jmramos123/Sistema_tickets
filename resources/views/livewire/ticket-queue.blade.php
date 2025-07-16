<div wire:poll.3000ms>

    {{-- Sidebar --}}
    <div class="bg-dark text-white position-fixed top-0 start-0 vh-100 d-flex flex-column p-3" style="width: 220px; z-index: 1000;">

        {{-- Top: Areas --}}
        <div>
            <h5 class="mb-4">Áreas</h5>
            @foreach($areas as $area)
            <button 
                wire:click="switchArea({{ $area->id }})"
                class="btn mb-2 text-start w-100 {{ $areaId == $area->id ? 'btn-primary' : 'btn-outline-light' }}">
                {{ $area->nombre_area }}
            </button>
            @endforeach
        </div>

        {{-- Bottom: Logout & Change Desk --}}
        <div class="mt-auto">
            <a href="{{ route('user.selectDesk') }}" class="btn btn-outline-light w-100 mb-2">
            Cambiar escritorio
            </a>
            <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100">
                Cerrar sesión
            </button>
            </form>
        </div>

    </div>


    {{-- Main content --}}
    <div class="p-4" style="margin-left: 220px;">

        <h2 class="mb-4">Área Actual: {{ $areas->find($areaId)->nombre_area }}</h2>

        <div class="d-flex flex-wrap gap-4">

            {{-- Adulto Mayor --}}
            <div class="flex-fill" style="min-width: 480px; flex: 1.2;">
                <h4 class="text-primary">Adulto Mayor</h4>
                <div class="border rounded p-3" style="height: 85vh; overflow-y: auto;">
                    @forelse($ticketsAdultoMayor as $index => $ticket)
                        <div class="d-flex justify-content-between align-items-center p-4 mb-3 border rounded 
                            {{ $index === 0 ? 'bg-light' : 'bg-secondary text-white opacity-75' }}">
                            <div>
                                <h3 class="mb-1">#{{ $ticket->numero_adulto_mayor }}</h3>
                                <small>Estado: {{ ucfirst($ticket->estado) }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button wire:click="llamar({{ $ticket->id }})" class="btn btn-success btn-lg"
                                    {{ $index !== 0 ? 'disabled' : '' }}>Llamar</button>
                                <button wire:click="confirmarDescartar({{ $ticket->id }})" class="btn btn-danger btn-lg"
                                    {{ $index !== 0 ? 'disabled' : '' }}>Descartar</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Sin tickets adulto mayor.</p>
                    @endforelse
                </div>
            </div>

            {{-- Normales --}}
            <div class="flex-fill" style="min-width: 480px; flex: 1.2;">
                <h4 class="text-primary">Normales</h4>
                <div class="border rounded p-3" style="height: 85vh; overflow-y: auto;">
                    @forelse($ticketsNormales as $index => $ticket)
                        <div class="d-flex justify-content-between align-items-center p-4 mb-3 border rounded 
                            {{ $index === 0 ? 'bg-light' : 'bg-secondary text-white opacity-75' }}">
                            <div>
                                <h3 class="mb-1">#{{ $ticket->numero }}</h3>
                                <small>Estado: {{ ucfirst($ticket->estado) }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button wire:click="llamar({{ $ticket->id }})" class="btn btn-success btn-lg"
                                    {{ $index !== 0 ? 'disabled' : '' }}>Llamar</button>
                                <button wire:click="confirmarDescartar({{ $ticket->id }})" class="btn btn-danger btn-lg"
                                    {{ $index !== 0 ? 'disabled' : '' }}>Descartar</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Sin tickets normales.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Modal --}}
    @if($confirmingTicket)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Descarte</h5>
                        <button type="button" class="btn-close" wire:click="$set('confirmingTicket', null)"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de descartar este ticket?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('confirmingTicket', null)">Cancelar</button>
                        <button type="button" class="btn btn-danger" wire:click="descartarConfirmado">Descartar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

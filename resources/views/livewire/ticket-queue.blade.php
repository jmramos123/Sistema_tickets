<div wire:poll.3000ms>

    {{-- Sidebar --}}
    <div class="bg-dark text-white position-fixed top-0 start-0 vh-100 d-flex flex-column p-3" style="width: 220px; z-index: 1000;">

        {{-- Top: Areas --}}
        <div>
            <h5 class="mb-4">Áreas</h5>
            @foreach($areas as $area)
                <button 
                    wire:click="switchArea({{ $area->id }})"
                    class="btn mb-2 text-start w-100 {{ !$verAtendidos && $areaId == $area->id ? 'btn-primary' : 'btn-outline-light' }}">
                    {{ $area->nombre_area }}
                </button>
            @endforeach
        </div>

        {{-- Bottom: Atendidos, Logout & Change Desk --}}
        <div class="mt-auto">
            <button wire:click="toggleAtendidos" class="btn {{ $verAtendidos ? 'btn-info' : 'btn-outline-info' }} w-100 mb-2">
                {{ $verAtendidos ? 'Volver a Turnos' : 'Atendidos' }}
            </button>
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

        <h2 class="mb-4">
            {{ $verAtendidos ? 'Tickets Atendidos' : 'Área Actual: ' . $areas->find($areaId)->nombre_area }}
        </h2>

        @if($verAtendidos)
            <div class="border rounded p-3" style="height: 85vh; overflow-y: auto;">
                @forelse($ticketsAtendidos as $ticket)
                    <div class="d-flex justify-content-between align-items-center p-4 mb-3 border rounded bg-secondary text-white">
                        <div>
                            <h3 class="mb-1">
                                @if($ticket->es_adulto_mayor)
                                    #{{ $ticket->numero_adulto_mayor }}
                                @else
                                    #{{ $ticket->numero }}
                                @endif
                            </h3>
                            <small>
                                Área: {{ $ticket->area->nombre_area }} | Estado: {{ ucfirst($ticket->estado) }}<br>
                                <span class="text-muted">Creado: {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                            </small>
                        </div>
                        <div>
                            <button wire:click="llamar({{ $ticket->id }})" class="btn btn-warning btn-lg">Volver a Llamar</button>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">No hay tickets atendidos.</p>
                @endforelse
            </div>
        @else
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
                                        {{ $index !== 0 ? 'disabled' : '' }}>Atendido</button>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">Sin tickets normales.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        @endif
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

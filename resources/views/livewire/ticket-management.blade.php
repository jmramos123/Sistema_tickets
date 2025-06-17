<div>
    @if (session()->has('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
    @endif

    <h2>Generar Ticket</h2>
    <form wire:submit.prevent="createTicket" class="mb-4">
        <div class="mb-2">
            <label>Área</label>
            <select wire:model="selectedArea" class="form-control" required>
                <option value="">Seleccione un área</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label><input type="checkbox" wire:model="es_adulto_mayor"> Adulto Mayor</label>
        </div>
        <button type="submit" class="btn btn-primary">Generar Ticket</button>
    </form>

    <h2>Tickets Pendientes</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Número</th>
                <th>Área</th>
                <th>Adulto Mayor</th>
                <th>Estado</th>
                <th>Generado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->numero }}</td>
                    <td>{{ $ticket->area->nombre }}</td>
                    <td>{{ $ticket->es_adulto_mayor ? 'Sí' : 'No' }}</td>
                    <td>{{ ucfirst($ticket->estado) }}</td>
                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Ticket en Llamado</h2>
    @if ($currentTicket)
        <div class="alert alert-warning">
            Llamando ticket: <strong>#{{ $currentTicket->numero }}</strong> (Área: {{ $currentTicket->area->nombre }})
        </div>
        <button wire:click="markAttended" class="btn btn-success">Marcar como Atendido</button>
    @else
        <div>No hay ticket en llamado.</div>
    @endif

    <button wire:click="callNextTicket" class="btn btn-primary mt-3">Llamar Siguiente Ticket</button>
</div>

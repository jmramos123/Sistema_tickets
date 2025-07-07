<div>
    <div class="flex flex-wrap items-end mb-4 gap-4">
        <!-- Poll to refresh when visible -->
        <div wire:poll.visible.100ms="$refresh"></div>

        <!-- Tipo de Ticket Filter -->
        <div>
            <label class="block text-sm font-medium">Tipo de Ticket</label>
            <select wire:model="filterTipo" class="border rounded p-2">
                <option value="">Todos</option>
                <option value="adulto_mayor">Adulto Mayor</option>
                <option value="normal">Normal</option>
            </select>
        </div>

        <!-- Fecha Desde -->
        <div>
            <label class="block text-sm font-medium">Desde</label>
            <input type="date" wire:model="dateFrom" class="border rounded p-2" />
        </div>

        <!-- Fecha Hasta -->
        <div>
            <label class="block text-sm font-medium">Hasta</label>
            <input type="date" wire:model="dateTo" class="border rounded p-2" />
        </div>

        <!-- Result count -->
        <div class="ml-auto text-sm">
            Mostrando: <span class="font-semibold">{{ $total }}</span> resultados
        </div>
    </div>

    <!-- Data table -->
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Ticket</th>
                <th class="px-4 py-2">Adulto Mayor</th>
                <th class="px-4 py-2">Escritorio</th>
                <th class="px-4 py-2">Usuario</th>
                <th class="px-4 py-2">Llamado En</th>
                <th class="px-4 py-2">Atendido En</th>
                <th class="px-4 py-2">Intentos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($llamadas as $item)
                <tr>
                    <td class="border px-4 py-2">{{ $item->id }}</td>
                    <td class="border px-4 py-2">{{ $item->ticket_id }}</td>
                    <td class="border px-4 py-2">{{ $item->es_adulto_mayor ? 'Sí' : 'No' }}</td>
                    <td class="border px-4 py-2">{{ $item->escritorio_id }}</td>
                    <td class="border px-4 py-2">{{ $item->usuario_id }}</td>
                    <td class="border px-4 py-2">{{ $item->llamado_en->format('Y-m-d H:i:s') }}</td>
                    <td class="border px-4 py-2">{{ $item->atendido_en->format('Y-m-d H:i:s') }}</td>
                    <td class="border px-4 py-2">{{ $item->intentos }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="border px-4 py-2 text-center">No hay resultados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $llamadas->links() }}
    </div>
</div>

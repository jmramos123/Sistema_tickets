<div>
    <h2>Gestión de Escritorios</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div class="d-flex">
            <input type="text" wire:model.defer="search" class="form-control w-100 me-2" placeholder="Buscar...">
            <button wire:click="applySearch" class="btn btn-outline-secondary">
                🔍 Buscar
            </button>
        </div>
        <button wire:click="openModal" class="btn btn-primary">Crear Escritorio</button>
    </div>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Área</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($escritorios as $escritorio)
                <tr>
                    <td>{{ $escritorio->nombre_escritorio }}</td>
                    <td>{{ $escritorio->area->nombre_area ?? '—' }}</td>
                    <td>
                        <button wire:click="edit({{ $escritorio->id }})" class="btn btn-sm btn-primary">Editar</button>
                        <button wire:click="delete({{ $escritorio->id }})" class="btn btn-sm btn-danger"
                                onclick="confirm('¿Eliminar escritorio?') || event.stopImmediatePropagation()">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($modalOpen)
        @include('livewire.escritorio-modal-form')
    @endif
</div>

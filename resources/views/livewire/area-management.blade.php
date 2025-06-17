<div>
    <h2>Gestión de Áreas</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <input type="text" wire:model="search" class="form-control w-25" placeholder="Buscar...">
        <button wire:click="openModal" class="btn btn-primary">Crear Área</button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($areas as $area)
                <tr>
                    <td>{{ $area->codigo_area }}</td>
                    <td>{{ $area->nombre_area }}</td>
                    <td>{{ $area->descripcion }}</td>
                    <td>
                        <button wire:click="edit({{ $area->id }})" class="btn btn-sm btn-primary">Editar</button>
                        <button wire:click="delete({{ $area->id }})" class="btn btn-sm btn-danger"
                                onclick="confirm('¿Eliminar área?') || event.stopImmediatePropagation()">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination (if you implement it later) --}}
    {{-- {{ $areas->links() }} --}}

    @if ($modalOpen)
        @include('livewire.area-modal-form')
    @endif
</div>

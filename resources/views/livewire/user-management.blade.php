<div>
    <h2>Gestión de Usuarios</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="d-flex flex-wrap justify-content-between mb-3 gap-2">


        <button wire:click="$refresh" class="btn btn-outline-secondary">Limpiar Filtros</button>

        <!-- Search -->
        <input type="text" wire:model.debounce.300ms="search" class="form-control w-25" placeholder="Buscar...">

        <!-- Filter by Área -->
        <select wire:model="filterArea" class="form-select w-25">
            <option value="">Todas las Áreas</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->nombre_area }}</option>
            @endforeach
        </select>

        <!-- Filter by Estado -->
        <select wire:model="filterStatus" class="form-select w-25">
            <option value="">Todos los Estados</option>
            <option value="enabled">Habilitado</option>
            <option value="disabled">Deshabilitado</option>
        </select>

        <!-- Create User Button -->
        <button wire:click="openModal" class="btn btn-primary">
            Crear Usuario
        </button>
    </div>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Username</th>
                <th>Área</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr wire:key="usuario-{{ $usuario->id }}">
                    <td>{{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}</td>
                    <td>{{ $usuario->persona->email }}</td>
                    <td>{{ $usuario->username }}</td>
                    <td>{{ $usuario->area->nombre_area ?? '—' }}</td>
                    <td>{{ $usuario->roles->pluck('name')->join(', ') }}</td>
                    <td>
                        <span class="badge bg-{{ $usuario->status === 'enabled' ? 'success' : 'secondary' }}">
                            {{ ucfirst($usuario->status) }}
                        </span>
                    </td>
                    <td>
                        <button wire:click="edit({{ $usuario->id }})" class="btn btn-sm btn-primary">Editar</button>
                        @if ($usuario->status === 'enabled')
                            <button wire:key="disable-button-{{ $usuario->id }}" wire:click="disableUser({{ $usuario->id }})" class="btn btn-sm btn-warning">Deshabilitar</button>
                        @else
                            <button wire:key="enable-button-{{ $usuario->id }}" wire:click="enableUser({{ $usuario->id }})" class="btn btn-sm btn-success">Habilitar</button>
                        @endif
                        <button wire:click="delete({{ $usuario->id }})" class="btn btn-sm btn-danger" onclick="confirm('¿Eliminar usuario?') || event.stopImmediatePropagation()">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $usuarios->links() }}

    @if ($modalOpen)
        @include('livewire.user-modal-form')
    @endif
</div>

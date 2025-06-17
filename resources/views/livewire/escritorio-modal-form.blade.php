<div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ $isEditMode ? 'Editar Escritorio' : 'Crear Escritorio' }}</h5>
                <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
            </div>

            <form wire:submit.prevent="save">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nombre del Escritorio</label>
                        <input type="text" wire:model.defer="nombre" class="form-control">
                        @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Área</label>
                        <select wire:model.defer="area_id" class="form-select">
                            <option value="">Seleccione un área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre_area }}</option>
                            @endforeach
                        </select>
                        @error('area_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">{{ $isEditMode ? 'Actualizar' : 'Crear' }}</button>
                </div>
            </form>

        </div>
    </div>
</div>

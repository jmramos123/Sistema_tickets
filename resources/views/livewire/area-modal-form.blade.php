<div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ $isEditMode ? 'Editar Área' : 'Crear Área' }}</h5>
                <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
            </div>

            <form wire:submit.prevent="save">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Código de Área</label>
                        <input type="text" wire:model.defer="codigo_area" class="form-control">
                        @error('codigo_area') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" wire:model.defer="nombre_area" class="form-control">
                        @error('nombre_area') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea wire:model.defer="descripcion" class="form-control" rows="3"></textarea>
                        @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
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

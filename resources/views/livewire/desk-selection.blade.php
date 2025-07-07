<div class="container vh-100 d-flex flex-column justify-content-center align-items-center">
    <h1 class="mb-5">Seleccione su Escritorio</h1>

    <div class="row w-100 justify-content-center">
        @foreach($desks as $desk)
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <button 
                    wire:click="selectDesk({{ $desk->id }})"
                    class="btn btn-primary btn-lg w-100 py-4 shadow"
                >
                    Escritorio {{ $desk->nombre_escritorio }}
                </button>
            </div>
        @endforeach
    </div>
</div>

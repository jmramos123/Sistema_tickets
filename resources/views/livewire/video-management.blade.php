<div wire:poll.3000ms>
    <h2 class="mb-4 fw-bold">Gestión de Videos</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    {{-- Video Upload Form --}}
    <form
    wire:submit.prevent="uploadVideo"
    class="card card-body mb-4 shadow-sm"
    enctype="multipart/form-data"
    >
    @csrf

    <div class="mb-3">
        <label>Nombre del video</label>
        <input
        type="text"
        wire:model.defer="nombre"
        class="form-control"
        placeholder="Ej: Publicidad 1"
        >
        @error('nombre') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label>Archivo de video</label>
        <input
        type="file"
        wire:model="videoFile"
        accept="video/*"
        class="form-control"
        >
        @error('videoFile') <div class="text-danger small">{{ $message }}</div> @enderror

        {{-- Progress UI --}}
        <div
        x-data="{ progress: 0 }"
        x-on:livewire-upload-start="progress = 0"
        x-on:livewire-upload-finish="progress = 100"
        x-on:livewire-upload-error="progress = 0"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
        class="mt-2"
        >
        <div class="progress">
            <div
            class="progress-bar"
            role="progressbar"
            :style="`width: ${progress}%`"
            ></div>
        </div>
        <div class="text-center mt-1 small" x-text="progress + '%'"></div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">📤 Subir Video</button>
    </form>

    {{-- Video List --}}
    <h4 class="mb-3 fw-semibold">Lista de Videos</h4>

    <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Nombre</th>
                <th>Archivo</th>
                <th>Subido en</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($videos as $video)
                <tr>
                    <td>{{ $video->nombre }}</td>
                    <td>
                        <a
                            href="{{ asset('storage/' . $video->ruta_archivo) }}"
                            target="_blank"
                            class="btn btn-sm btn-outline-primary"
                        >Ver Video</a>
                    </td>
                    <td>{{ $video->uploaded_at }}</td>
                    <td>
                        @if($video->is_active)
                            <span class="badge bg-success">🎬 Reproduciendo</span>
                        @else
                            <span class="badge bg-secondary">En espera</span>
                        @endif
                    </td>
                    <td>
                        <button
                            wire:click="delete({{ $video->id }})"
                            onclick="return confirm('¿Eliminar este video?')"
                            class="btn btn-danger btn-sm"
                        >🗑️ Eliminar</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Sin videos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('video-upload-form');
    const progressBar = document.getElementById('upload-progress-bar');
    const progressContainer = document.getElementById('upload-progress-container');
    const uploadButton = document.getElementById('upload-button');
    const statusText = document.getElementById('upload-status');
    const nombreError = document.getElementById('nombre-error');
    const videoFileError = document.getElementById('video-file-error');

    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset errors
        nombreError.textContent = '';
        videoFileError.textContent = '';

        const nombre = document.getElementById('video-name').value;
        const fileInput = document.getElementById('video-file-input');
        const file = fileInput.files[0];

        // Basic validation
        if (!nombre || nombre.length < 3) {
            nombreError.textContent = 'El nombre debe tener al menos 3 caracteres';
            return;
        }
        if (!file) {
            videoFileError.textContent = 'Por favor seleccione un archivo de video';
            return;
        }

        const validTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/webm'];
        const maxSize = 50 * 1024 * 1024; // 50MB
        if (!validTypes.includes(file.type)) {
            videoFileError.textContent = 'Formato no válido. Use MP4, AVI, MOV o WEBM.';
            return;
        }
        if (file.size > maxSize) {
            videoFileError.textContent = 'El archivo es demasiado grande (máx 50MB)';
            return;
        }

        // Build FormData
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('videoFile', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        uploadButton.disabled = true;
        progressContainer.style.display = 'block';
        statusText.textContent = 'Subiendo...';

        // Send via XHR
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("video.upload") }}', true);

        xhr.upload.addEventListener('progress', (event) => {
            if (event.lengthComputable) {
                const percent = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = `${percent}%`;
                statusText.textContent = `${percent}%`;
            }
        });

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    statusText.textContent = '¡Subida completada!';
                    Livewire.dispatch('videoUploaded');
                    form.reset();
                } else {
                    videoFileError.textContent = response.message;
                }
            } else {
                videoFileError.textContent = `Error del servidor: ${xhr.status}`;
            }

            setTimeout(() => {
                progressContainer.style.display = 'none';
                uploadButton.disabled = false;
            }, 2000);
        };

        xhr.onerror = function() {
            videoFileError.textContent = 'Error de conexión';
            uploadButton.disabled = false;
            progressContainer.style.display = 'none';
        };

        xhr.send(formData);
    });
});
</script>
@endpush

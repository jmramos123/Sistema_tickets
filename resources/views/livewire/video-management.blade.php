<div>
    <h2>Gestión de Videos</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form id="video-upload-form" class="mb-4">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nombre del video</label>
            <input type="text" id="video-name" name="nombre" class="form-control" placeholder="Nombre del video">
            <div id="nombre-error" class="text-danger"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Archivo de video</label>
            <input type="file" name="videoFile" id="video-file-input" accept="video/*" class="form-control">
            <div id="video-file-error" class="text-danger"></div>
            
            <div class="mt-2" id="upload-progress-container" style="display:none">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" id="upload-progress-bar" style="width: 0%"></div>
                </div>
                <div class="text-center mt-1" id="upload-status">0%</div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary" id="upload-button">
            Subir Video
        </button>
    </form>

    <h4>Lista de Videos</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Archivo</th>
                <th>Subido en</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($videos as $video)
                <tr>
                    <td>{{ $video->nombre }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $video->ruta_archivo) }}" target="_blank">Ver Video</a>
                    </td>
                    <td>{{ $video->uploaded_at }}</td>
                    <td>
                        <button wire:click="delete({{ $video->id }})" 
                                onclick="return confirm('¿Eliminar este video?')"
                                class="btn btn-danger btn-sm">
                            Eliminar
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('video-upload-form');
    const progressBar = document.getElementById('upload-progress-bar');
    const progressContainer = document.getElementById('upload-progress-container');
    const uploadButton = document.getElementById('upload-button');
    const statusText = document.getElementById('upload-status');
    const nombreError = document.getElementById('nombre-error');
    const videoFileError = document.getElementById('video-file-error');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset errors
        nombreError.textContent = '';
        videoFileError.textContent = '';
        
        // Get form data
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
        
        // Validate file type and size
        const validTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/webm'];
        const maxSize = 50 * 1024 * 1024; // 50MB
        
        if (!validTypes.includes(file.type)) {
            videoFileError.textContent = 'Formato de archivo no válido. Use MP4, AVI, MOV o WEBM.';
            return;
        }
        
        if (file.size > maxSize) {
            videoFileError.textContent = 'El archivo es demasiado grande (máximo 50MB)';
            return;
        }
        
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('videoFile', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        uploadButton.disabled = true;
        progressContainer.style.display = 'block';
        statusText.textContent = 'Subiendo...';
        
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
                    // Refresh the Livewire component
                    Livewire.dispatch('videoUploaded');
                    // Reset form
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Add this line -->
    <title>{{ $title ?? 'Panel de Administración' }}</title>
    

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom Styles --}}
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 220px;
            background-color: #343a40;
            flex-shrink: 0;
        }
        .sidebar .nav-link {
            color: #ddd;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background-color: #495057;
            color: #fff;
        }
        .content {
            flex-grow: 1;
            padding: 1.5rem;
            background-color: #f8f9fa;
        }
    </style>

    @livewireStyles
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar d-flex flex-column p-3">
        <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
            <span class="fs-5 fw-bold">Sistema Tickets</span>
        </a>

        <hr class="text-white">

        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    Usuarios
                </a>
            </li>
            <li>
                <a href="{{ route('areas.index') }}" class="nav-link {{ request()->routeIs('areas.index') ? 'active' : '' }}">
                    Áreas
                </a>
            </li>
            <li>
                <a href="{{ route('admin.escritorios') }}" class="nav-link {{ request()->routeIs('admin.escritorios') ? 'active' : '' }}">
                    Escritorios
                </a>
            </li>
            <li>
                <a href="{{ route('videos.index') }}" class="nav-link {{ request()->routeIs('videos.index') ? 'active' : '' }}">
                    Videos
                </a>
            </li>
            <li>
                <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                    Tickets
                </a>
            </li>
        </ul>

        <hr class="text-white">

        <div>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="btn btn-outline-light w-100">Cerrar sesión</a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">{{ $title ?? 'Panel de Administración' }}</h2>
        </div>

        {{ $slot }}
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('upload.start', (file) => {
                if (!file) return;
            
            // Ensure file has name property
                if (!file.name && file.file) {
                    file.name = file.file.name;
                }
            
            // Fallback name generation
                if (!file.name) {
                    file.name = 'video_' + Date.now() + '.mp4';
                }
        });
        });
    </script>
    @livewireScripts

</body>
</html>

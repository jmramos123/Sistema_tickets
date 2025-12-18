<x-layouts.app :title="__('Bienvenido Administrador')">
    <div class="container py-5 text-center bg-white">
        <h1 class="display-3 fw-bold mb-4 text-dark">
            👋 ¡Bienvenido, Administrador!
        </h1>
        <p class="fs-4 text-secondary mb-5 mx-auto" style="max-width: 800px;">
            Este es tu espacio de control para gestionar todo el sistema de turnos y pantallas informativas.
            Siéntete libre de navegar, configurar y personalizar todo a tu manera.
        </p>

        <div class="row g-4 justify-content-center">
            <div class="col-12 col-md-4">
                <div class="card shadow-lg border-0 h-100" style="background: linear-gradient(to bottom right, #3b82f6, #6366f1); color: black;">
                    <div class="card-body">
                        <h2 class="h4 fw-semibold mb-2">⚙️ Configuración</h2>
                        <p class="small opacity-75 mb-0">Administra las áreas, escritorios y usuarios.</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card shadow-lg border-0 h-100" style="background: linear-gradient(to bottom right, #10b981, #0d9488); color: black;">
                    <div class="card-body">
                        <h2 class="h4 fw-semibold mb-2">🎥 Videos en TV</h2>
                        <p class="small opacity-75 mb-0">Controla y sube los videos publicitarios del sistema.</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card shadow-lg border-0 h-100" style="background: linear-gradient(to bottom right, #f43f5e, #ec4899); color: black;">
                    <div class="card-body">
                        <h2 class="h4 fw-semibold mb-2">🎫 Turnos</h2>
                        <p class="small opacity-75 mb-0">Emisión y seguimiento de tickets en tiempo real.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 text-muted small">
            &copy; {{ date('Y') }} Sistema de Gestión de Tickets — UGE.
        </div>
    </div>
</x-layouts.app>

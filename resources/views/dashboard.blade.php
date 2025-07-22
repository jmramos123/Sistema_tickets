<x-layouts.app :title="__('Bienvenido Administrador')">
    <div class="flex flex-col items-center justify-center text-center py-24 px-4 bg-white">
        <h1 class="text-6xl font-extrabold mb-6 text-gray-900">
            👋 ¡Bienvenido, Administrador!
        </h1>
        <p class="text-xl text-gray-700 mb-12 max-w-3xl">
            Este es tu espacio de control para gestionar todo el sistema de turnos y pantallas informativas.
            Siéntete libre de navegar, configurar y personalizar todo a tu manera.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-5xl">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-8 rounded-xl text-black shadow-lg">
                <h2 class="text-2xl font-semibold mb-2">⚙️ Configuración</h2>
                <p class="text-sm opacity-90">Administra las áreas, escritorios y usuarios.</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-8 rounded-xl text-black shadow-lg">
                <h2 class="text-2xl font-semibold mb-2">🎥 Videos en TV</h2>
                <p class="text-sm opacity-90">Controla y sube los videos publicitarios del sistema.</p>
            </div>

            <div class="bg-gradient-to-br from-rose-500 to-pink-600 p-8 rounded-xl text-black shadow-lg">
                <h2 class="text-2xl font-semibold mb-2">🎫 Turnos</h2>
                <p class="text-sm opacity-90">Emisión y seguimiento de tickets en tiempo real.</p>
            </div>
        </div>

        <div class="mt-16 text-gray-500 text-sm">
            &copy; {{ date('Y') }} Sistema de Gestión de Tickets — UGE.
        </div>
    </div>
</x-layouts.app>

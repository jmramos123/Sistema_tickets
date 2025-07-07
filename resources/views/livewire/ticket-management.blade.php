<div>
    {{-- Filters --}}
    <div class="card mb-4 shadow-sm p-3 rounded">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="startDate" class="form-label fw-semibold">Desde:</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-calendar-alt text-muted"></i>
                    </span>
                    <input type="date" id="startDate" wire:model="startDate" class="form-control border-start-0" placeholder="Fecha inicio" />
                </div>
            </div>
            <div class="col-md-4">
                <label for="endDate" class="form-label fw-semibold">Hasta:</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-calendar-alt text-muted"></i>
                    </span>
                    <input type="date" id="endDate" wire:model="endDate" class="form-control border-start-0" placeholder="Fecha fin" />
                </div>
            </div>
            <div class="col-md-4">
                <label for="ticketType" class="form-label fw-semibold">Tipo:</label>
                <select id="ticketType" wire:model="ticketType" class="form-select">
                    <option value="all">Todos</option>
                    <option value="normal">Normal</option>
                    <option value="adulto_mayor">Adulto Mayor</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Reactive tracking --}}
    <div class="d-none">
        {{ $startDate }}{{ $endDate }}{{ $ticketType }}
    </div>

    {{-- Auto refresh --}}
    <div wire:poll.visible.100ms="$refresh"></div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 bg-primary bg-opacity-10 border rounded text-primary fw-bold text-center">
                Total Filtrado<br>
                <span class="fs-4">{{ $filteredTotal }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-warning bg-opacity-10 border rounded text-warning text-center">
                Promedio Espera<br>
                <span class="fs-4">{{ gmdate('i:s', $waitAvgSeconds) }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-success bg-opacity-10 border rounded text-success text-center">
                Promedio Atención<br>
                <span class="fs-4">{{ gmdate('i:s', $attentionAvgSeconds) }}</span>
            </div>
        </div>
    </div>

    {{-- Hidden data for JS --}}
    <div id="chart-data" class="d-none">
        <span id="chart-labels">@json($chartLabels)</span>
        <span id="chart-counts">@json($chartCounts)</span>
    </div>

    {{-- Chart --}}
    <div id="chart-wrapper" wire:ignore style="width:90%; max-width:800px; height:400px; margin:auto;">
        <canvas id="ticketsChart"></canvas>
    </div>

    {{-- Top lists --}}
    <div class="row mt-5 g-4">
        <div class="col-md-4">
            <h5 class="fw-bold mb-3 text-primary">Top Escritorios</h5>
            <ul class="list-group">
                @forelse($topDesks as $deskName => $count)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $deskName }}
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted fst-italic">Sin datos para este rango.</li>
                @endforelse
            </ul>
        </div>

        <div class="col-md-4">
            <h5 class="fw-bold mb-3 text-success">Top Empleados</h5>
            <ul class="list-group">
                @forelse($topEmployees as $employeeName => $count)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $employeeName }}
                        <span class="badge bg-success rounded-pill">{{ $count }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted fst-italic">Sin datos para este rango.</li>
                @endforelse
            </ul>
        </div>

        <div class="col-md-4">
            <h5 class="fw-bold mb-3 text-warning">Top Áreas</h5>
            <ul class="list-group">
                @forelse($topAreas as $areaName => $count)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $areaName }}
                        <span class="badge bg-warning rounded-pill">{{ $count }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted fst-italic">Sin datos para este rango.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chart;

// Always attach the listener globally
document.addEventListener('updatedFilters', () => {
    console.log('📡 Livewire "updatedFilters" event received');
    // Wait a tick to let Livewire update the DOM
    requestAnimationFrame(() => {
        renderChart();
    });
});

function getChartData() {
    try {
        const labels = JSON.parse(document.getElementById('chart-labels')?.textContent || '[]');
        const counts = JSON.parse(document.getElementById('chart-counts')?.textContent || '[]');
        return { labels, counts };
    } catch (e) {
        console.warn('❌ Could not parse chart data', e);
        return { labels: [], counts: [] };
    }
}

function renderChart() {
    const { labels, counts } = getChartData();
    const ctx = document.getElementById('ticketsChart')?.getContext('2d');
    if (!ctx) return;

    if (chart) chart.destroy();

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Tickets emitidos',
                data: counts,
                tension: 0.2,
                fill: true,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.1)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { title: { display: true, text: 'Fecha' } },
                y: { beginAtZero: true, title: { display: true, text: 'Cantidad' } }
            }
        }
    });

    console.log('✅ Chart rendered:', labels, counts);
}
// Initial chart render
document.addEventListener('DOMContentLoaded', renderChart);
</script>
@endpush

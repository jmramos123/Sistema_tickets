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
    <div wire:poll.visible.1000ms="$refresh"></div>

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
    {{-- Line chart data --}}
    <span id="chart-labels">@json($chartLabels)</span>
    <span id="chart-counts">@json($chartCounts)</span>

    {{-- Bar chart data --}}
    <span id="desk-labels">@json(array_keys($topDesks))</span>
    <span id="desk-counts">@json(array_values($topDesks))</span>

    <span id="employee-labels">@json(array_keys($topEmployees))</span>
    <span id="employee-counts">@json(array_values($topEmployees))</span>

    <span id="area-labels">@json(array_keys($topAreas))</span>
    <span id="area-counts">@json(array_values($topAreas))</span>
    </div>

    {{-- Chart --}}
    <div id="chart-wrapper" wire:ignore style="width:90%; max-width:800px; height:400px; margin:auto;">
        <canvas id="ticketsChart"></canvas>
    </div>

    {{-- Top lists --}}
    {{-- Below the main line chart --}}
    <div class="row mt-4">
    <div class="col-md-4">
        <h6 class="text-primary">Top Escritorios</h6>
        <div wire:ignore>
        <canvas id="desksBarChart" style="width:100%; height:200px;"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <h6 class="text-success">Top Empleados</h6>
        <div wire:ignore>
        <canvas id="employeesBarChart" style="width:100%; height:200px;"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <h6 class="text-warning">Top Áreas</h6>
        <div wire:ignore>
        <canvas id="areasBarChart" style="width:100%; height:200px;"></canvas>
        </div>
    </div>
    </div>

</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chart, desksChart, employeesChart, areasChart;

// Re-render all charts when filters update
document.addEventListener('updatedFilters', () => {
    console.log('📡 Livewire "updatedFilters" event received');
    requestAnimationFrame(renderAllCharts);
});

// Utility to parse hidden JSON spans
function getChartData(idLabels, idCounts) {
    try {
        const labels = JSON.parse(document.getElementById(idLabels)?.textContent || '[]');
        const counts = JSON.parse(document.getElementById(idCounts)?.textContent || '[]');
        return { labels, counts };
    } catch (e) {
        console.warn(`❌ Could not parse data for ${idLabels}/${idCounts}`, e);
        return { labels: [], counts: [] };
    }
}

// Line chart
function renderLineChart() {
    const { labels, counts } = getChartData('chart-labels', 'chart-counts');
    const ctx = document.getElementById('ticketsChart')?.getContext('2d');
    if (!ctx) return;
    if (chart) chart.destroy();
    chart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label: 'Tickets emitidos', data: counts, tension: 0.2, fill: true }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
}

// Bar charts
function renderDesksBarChart() {
    const { labels, counts } = getChartData('desk-labels', 'desk-counts');
    const ctx = document.getElementById('desksBarChart')?.getContext('2d');
    if (!ctx) return;
    if (desksChart) desksChart.destroy();
    desksChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Llamadas por escritorio', data: counts, barThickness: 30 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
}

function renderEmployeesBarChart() {
    const { labels, counts } = getChartData('employee-labels', 'employee-counts');
    const ctx = document.getElementById('employeesBarChart')?.getContext('2d');
    if (!ctx) return;
    if (employeesChart) employeesChart.destroy();
    employeesChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Llamadas por empleado', data: counts, barThickness: 30 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
}

function renderAreasBarChart() {
    const { labels, counts } = getChartData('area-labels', 'area-counts');
    const ctx = document.getElementById('areasBarChart')?.getContext('2d');
    if (!ctx) return;
    if (areasChart) areasChart.destroy();
    areasChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Llamadas por área', data: counts, barThickness: 30 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
}

// Render all charts
function renderAllCharts() {
    renderLineChart();
    renderDesksBarChart();
    renderEmployeesBarChart();
    renderAreasBarChart();
}

// Initial render on load
document.addEventListener('DOMContentLoaded', renderAllCharts);
</script>
@endpush

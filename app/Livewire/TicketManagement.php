<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Llamada;
use Carbon\Carbon;

class TicketManagement extends Component
{
    public $topDesks = [];
    public $topEmployees = [];
    public $topAreas = [];
    public $waitAvgSeconds = 0;
    public $attentionAvgSeconds = 0;
    public $filteredTotal = 0;
    public $startDate;
    public $endDate;
    public $ticketType = 'all'; // all | normal | adulto_mayor

    public $chartLabels = [];
    public $chartCounts = [];

    public function mount()
    {
        $this->startDate = Carbon::today()->toDateString();
        $this->endDate   = Carbon::today()->toDateString();
        $this->fetchChartData();
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'ticketType'])) {
            $this->fetchChartData();
            logger("📥 updated() called: $property");
            $this->dispatch('updatedFilters');
        }
    }

    protected function fetchChartData()
    {
        $labels = [];
        $counts = [];

        $waitTimes = [];
        $attentionTimes = [];

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $period = $start->daysUntil($end);

        foreach ($period as $date) {
            $labels[] = $date->format('Y-m-d');

            $query = Llamada::whereDate('llamado_en', $date);

            if ($this->ticketType === 'adulto_mayor') {
                $query->where('es_adulto_mayor', 1);
            } elseif ($this->ticketType === 'normal') {
                $query->where('es_adulto_mayor', 0);
            }

            $llamadas = $query->get();
            $counts[] = $llamadas->count();

            foreach ($llamadas as $llamada) {
                if ($llamada->llamado_en && $llamada->atendido_en) {
                    $waitSecs = abs(Carbon::parse($llamada->atendido_en)->diffInSeconds(Carbon::parse($llamada->llamado_en), false));
                    $waitTimes[] = $waitSecs;
                }
                if ($llamada->created_at && $llamada->updated_at) {
                    $attentionSecs = abs(Carbon::parse($llamada->updated_at)->diffInSeconds(Carbon::parse($llamada->created_at), false));
                    $attentionTimes[] = $attentionSecs;
                }
            }
        }

        $this->filteredTotal = array_sum($counts);
        $this->chartLabels = $labels;
        $this->chartCounts = $counts;
        $this->waitAvgSeconds = count($waitTimes) ? round(array_sum($waitTimes) / count($waitTimes), 2) : 0;
        $this->attentionAvgSeconds = count($attentionTimes) ? round(array_sum($attentionTimes) / count($attentionTimes), 2) : 0;

        // 🚀 Top 3 Escritorios by name
        $deskQuery = Llamada::selectRaw('escritorios.nombre_escritorio, COUNT(*) as total')
            ->join('escritorios', 'llamadas.escritorio_id', '=', 'escritorios.id')
            ->whereBetween('llamado_en', [$start, $end]);

        if ($this->ticketType === 'adulto_mayor') {
            $deskQuery->where('es_adulto_mayor', 1);
        } elseif ($this->ticketType === 'normal') {
            $deskQuery->where('es_adulto_mayor', 0);
        }

        $this->topDesks = $deskQuery->groupBy('escritorios.nombre_escritorio')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('total', 'escritorios.nombre_escritorio')
            ->toArray();

        // 🚀 Top 3 Empleados by full name
        $employeeQuery = Llamada::selectRaw("CONCAT(personas.nombre, ' ', personas.apellido) as empleado, COUNT(*) as total")
            ->join('usuarios', 'llamadas.usuario_id', '=', 'usuarios.id')
            ->join('personas', 'usuarios.persona_id', '=', 'personas.id')
            ->whereBetween('llamado_en', [$start, $end]);

        if ($this->ticketType === 'adulto_mayor') {
            $employeeQuery->where('es_adulto_mayor', 1);
        } elseif ($this->ticketType === 'normal') {
            $employeeQuery->where('es_adulto_mayor', 0);
        }

        $this->topEmployees = $employeeQuery->groupBy('empleado')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('total', 'empleado')
            ->toArray();

        // 🚀 Top 3 Áreas by name
        $areaQuery = Llamada::selectRaw('areas.nombre_area, COUNT(*) as total')
            ->join('escritorios', 'llamadas.escritorio_id', '=', 'escritorios.id')
            ->join('areas', 'escritorios.area_id', '=', 'areas.id')
            ->whereBetween('llamado_en', [$start, $end]);

        if ($this->ticketType === 'adulto_mayor') {
            $areaQuery->where('es_adulto_mayor', 1);
        } elseif ($this->ticketType === 'normal') {
            $areaQuery->where('es_adulto_mayor', 0);
        }

        $this->topAreas = $areaQuery->groupBy('areas.nombre_area')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('total', 'areas.nombre_area')
            ->toArray();

        logger()->debug('✅ Chart + Top Data', [
            'labels' => $labels,
            'counts' => $counts,
            'wait_avg_secs' => $this->waitAvgSeconds,
            'attention_avg_secs' => $this->attentionAvgSeconds,
            'top_desks' => $this->topDesks,
            'top_employees' => $this->topEmployees,
            'top_areas' => $this->topAreas
        ]);
    }

    function renderChart() {
        // ...
    }
    public function render()
    {
        return view('livewire.ticket-management');
    }
}

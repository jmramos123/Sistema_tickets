<?php
// File: app/Http/Livewire/LlamadasTable.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Llamada;

class Test extends Component
{
    use WithPagination;

    public $filterTipo = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = [
        'filterTipo' => ['except' => ''],
        'dateFrom'   => ['except' => ''],
        'dateTo'     => ['except' => ''],
    ];

    public function updatingFilterTipo()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }
    public function updated($property)
    {
        $this->dispatch('updatedFilters');
    }


    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Llamada::query();

        if ($this->filterTipo === 'adulto_mayor') {
            $query->where('es_adulto_mayor', 1);
        } elseif ($this->filterTipo === 'normal') {
            $query->where('es_adulto_mayor', 0);
        }

        if ($this->dateFrom) {
            $query->whereDate('llamado_en', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('llamado_en', '<=', $this->dateTo);
        }

        $llamadas = $query->orderBy('llamado_en', 'desc')->paginate(10);

        $total = $llamadas->total();

        return view('livewire.test', [
            'llamadas' => $llamadas,
            'total'    => $total,
        ]);
    }
}

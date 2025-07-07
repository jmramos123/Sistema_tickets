<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use App\Models\Ticket;

class ClientTicket extends Component
{
    use WithPagination;

    public $step = 'selectArea';       // selectArea | selectType | printTicket
    public $selectedArea = null;
    public $isAdultoMayor = false;
    public $ticket = null;

    // Show 4 areas per “page”
    protected $perPage = 4;

    protected $paginationTheme = 'bootstrap';

    public function selectArea(int $areaId)
    {
        $this->selectedArea = Area::findOrFail($areaId);
        $this->step = 'selectType';
    }

    public function issueTicket(bool $adultoMayor)
    {
        $this->isAdultoMayor = $adultoMayor;

        if ($adultoMayor) {
            $lastNumber = Ticket::where('es_adulto_mayor', true)->max('numero_adulto_mayor');
            $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

            $this->ticket = Ticket::create([
                'area_id'               => $this->selectedArea->id,
                'numero_adulto_mayor'   => $nextNumber,
                'es_adulto_mayor'       => true,
                'estado'                => 'pendiente',
                'created_at'            => now(),
            ]);
        } else {
            $lastNumber = Ticket::where('es_adulto_mayor', false)->max('numero');
            $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

            $this->ticket = Ticket::create([
                'area_id'         => $this->selectedArea->id,
                'numero'          => $nextNumber,
                'es_adulto_mayor' => false,
                'estado'          => 'pendiente',
                'created_at'      => now(),
            ]);
        }

        $this->step = 'printTicket';
    }


    public function resetToMenu()
    {
        $this->resetPage();     // reset pagination back to page 1
        $this->step          = 'selectArea';
        $this->selectedArea  = null;
        $this->isAdultoMayor = false;
        $this->ticket        = null;
    }

    public function render()
    {
        return view('livewire.client-ticket', [
            'areas' => Area::orderBy('codigo_area')
                           ->paginate($this->perPage),
        ])->layout('components.layouts.client');
    }
}

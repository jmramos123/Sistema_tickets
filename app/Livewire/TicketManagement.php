<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Area;

class TicketManagement extends Component
{
    public $tickets;
    public $areas;
    public $selectedArea;
    public $es_adulto_mayor = false;

    public $currentTicket = null;

    public function mount()
    {
        $this->areas = Area::all();
        $this->loadTickets();
    }

    public function loadTickets()
    {
        $this->tickets = Ticket::where('estado', 'pendiente')
            ->orderByDesc('es_adulto_mayor')
            ->orderBy('created_at')
            ->get();
    }

    public function generateNumero()
    {
        // Get last numero for selected area
        $lastTicket = Ticket::where('area_id', $this->selectedArea)
            ->latest('numero')
            ->first();

        return $lastTicket ? $lastTicket->numero + 1 : 1;
    }

    public function createTicket()
    {
        $this->validate([
            'selectedArea' => 'required|exists:areas,id',
            'es_adulto_mayor' => 'boolean',
        ]);

        $numero = $this->generateNumero();

        Ticket::create([
            'area_id' => $this->selectedArea,
            'numero' => $numero,
            'es_adulto_mayor' => $this->es_adulto_mayor,
            'estado' => 'pendiente',
        ]);

        session()->flash('message', "Ticket #$numero generado correctamente.");

        $this->selectedArea = null;
        $this->es_adulto_mayor = false;

        $this->loadTickets();
    }

    public function callNextTicket()
    {
        $nextTicket = Ticket::where('estado', 'pendiente')
            ->orderByDesc('es_adulto_mayor')
            ->orderBy('created_at')
            ->first();

        if (!$nextTicket) {
            session()->flash('message', 'No hay tickets pendientes.');
            return;
        }

        if ($this->currentTicket) {
            $this->currentTicket->estado = 'atendido';
            $this->currentTicket->save();
        }

        $nextTicket->estado = 'llamado';
        $nextTicket->save();

        $this->currentTicket = $nextTicket;

        $this->loadTickets();
    }

    public function markAttended()
    {
        if (!$this->currentTicket) {
            session()->flash('message', 'No hay ticket en proceso.');
            return;
        }

        $this->currentTicket->estado = 'atendido';
        $this->currentTicket->save();

        $this->currentTicket = null;

        $this->loadTickets();

        session()->flash('message', 'Ticket atendido correctamente.');
    }

    public function render()
    {
        return view('livewire.ticket-management');
    }
}

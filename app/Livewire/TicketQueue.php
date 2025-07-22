<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Llamada;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Events\TicketCalled;
use Carbon\Carbon;

class TicketQueue extends Component
{
    public $verAtendidos = false;
    public $ticketsAtendidos = [];
    public $normalesLeft = 0;
    public $adultosLeft = 0;
    public $escritorioId;
    public $areaId;
    public $areas;
    public $ticketsNormales = [];
    public $ticketsAdultoMayor = [];
    public $confirmingTicket = null;

    public function confirmarDescartar($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        if ($ticket->estado === 'pendiente') {
            $this->confirmingTicket = $ticketId;
        } else {
            $this->descartar($ticketId);
        }
    }

    public function descartarConfirmado()
    {
        $this->descartar($this->confirmingTicket);
        $this->confirmingTicket = null;
    }

    public function mount()
    {
        $this->escritorioId = Session::get('escritorio_id');
        $this->areaId = Auth::user()->area_id;

        if (!$this->escritorioId) {
            return redirect()->route('user.desks');
        }

        $this->deleteOldTickets();
        $this->areas = Area::where('id', '!=', 1) // or ->where('id', '!=', 1)
            ->orderBy('nombre_area')
            ->get();
        $this->loadTickets();
    }

    protected function deleteOldTickets()
    {
        Ticket::whereDate('created_at', '<', now()->toDateString())->delete();
    }

    public function toggleAtendidos()
    {
        $this->verAtendidos = !$this->verAtendidos;
        $this->loadTickets();
    }

    public function loadTickets()
    {
        if ($this->verAtendidos) {
            $this->ticketsAtendidos = Ticket::where('estado', 'atendido')
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();
            return;
        }

        $this->ticketsNormales = Ticket::where('area_id', $this->areaId)
            ->where('es_adulto_mayor', false)
            ->whereIn('estado', ['pendiente', 'llamado'])  // include both statuses
            ->orderBy('numero')
            ->take(5)
            ->get();

        $this->ticketsAdultoMayor = Ticket::where('area_id', $this->areaId)
            ->where('es_adulto_mayor', true)
            ->whereIn('estado', ['pendiente', 'llamado'])  // include both statuses
            ->orderBy('numero')
            ->take(5)
            ->get();

        $this->normalesLeft = Ticket::where('area_id', $this->areaId)
            ->where('es_adulto_mayor', false)
            ->where('estado', 'pendiente')
            ->count() - $this->ticketsNormales->count();

        $this->adultosLeft = Ticket::where('area_id', $this->areaId)
            ->where('es_adulto_mayor', true)
            ->where('estado', 'pendiente')
            ->count() - $this->ticketsAdultoMayor->count();
    }

    public function switchArea($newAreaId)
    {
        $this->areaId = $newAreaId;
        $this->loadTickets();
    }

    public function llamar($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $now = now();

        // Record the call (incrementing attempts, etc.)
        $llamada = Llamada::firstOrNew(['ticket_id' => $ticket->id]);
        if (!$llamada->exists) {
            $llamada->escritorio_id   = $this->escritorioId;
            $llamada->usuario_id      = Auth::id();
            $llamada->es_adulto_mayor = $ticket->es_adulto_mayor;
            $llamada->llamado_en      = $ticket->created_at;
            $llamada->intentos        = 1;
        } else {
            $llamada->intentos++;
            $llamada->usuario_id = $llamada->usuario_id ?? Auth::id();
            // If you want to treat this recall as a fresh call time:
            $llamada->llamado_en = $now;
        }
        $llamada->save();

        // **Only change estado if it was still pendiente**
        if ($ticket->estado === 'pendiente') {
            $ticket->estado = 'llamado';
            // Optionally reset created_at so it flows through your queue logic
            // $ticket->created_at = $now;
            $ticket->save();
        }

        // Broadcast the call in all cases
        event(new TicketCalled($ticket->id));

        // Reload lists
        $this->loadTickets();
    }

    public function descartar($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $llamada = Llamada::where('ticket_id', $ticketId)->first();

        if ($llamada) {
            $llamada->atendido_en = now();
            if (!$llamada->usuario_id) {
                $llamada->usuario_id = Auth::id();
            }
            $llamada->save();
        }

        $ticket->estado = 'atendido';
        $ticket->save();

        $this->loadTickets();
    }

    public function render()
    {
        $this->loadTickets();
        return view('livewire.ticket-queue')->layout('components.layouts.client');
    }
}

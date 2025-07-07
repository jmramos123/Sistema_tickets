<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Llamada;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class TicketQueue extends Component
{
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

        $this->areas = Area::orderBy('nombre_area')->get();
        $this->loadTickets();
    }

    public function loadTickets()
    {
        $this->ticketsNormales = Ticket::where('area_id', $this->areaId)
            ->where('es_adulto_mayor', false)
            ->orderBy('numero')
            ->take(10)
            ->get();

        $this->ticketsAdultoMayor = Ticket::where('area_id', $this->areaId)
            ->where('es_adulto_mayor', true)
            ->orderBy('numero')
            ->take(10)
            ->get();
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

        $llamada = Llamada::firstOrNew(['ticket_id' => $ticket->id]);

        if (!$llamada->exists) {
            $llamada->escritorio_id    = $this->escritorioId;
            $llamada->usuario_id       = Auth::id();
            $llamada->es_adulto_mayor  = $ticket->es_adulto_mayor;

            // ❌ Before: $llamada->llamado_en = $now;
            // ✅ Correct: use ticket creation time
            $llamada->llamado_en       = $ticket->created_at;
            $llamada->intentos         = 1;
        } else {
            $llamada->intentos++;
            if (!$llamada->usuario_id) {
                $llamada->usuario_id = Auth::id();
            }
        }

        $llamada->save();

        $ticket->estado = 'llamado';
        $ticket->save();

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

        $ticket->delete();
        $this->loadTickets();
    }

    public function render()
    {
        $this->loadTickets();
        return view('livewire.ticket-queue')->layout('components.layouts.client');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use App\Models\Ticket;

class TvDisplay extends Component
{
    public $activeVideo;
    public $nextTickets = [];
    public $calledTicket = null;

    protected $listeners = [
        'call-ticket' => 'onCallTicket',
    ];

    public function mount()
    {
        if (session()->has('last_called_ticket')) {
            $this->onCallTicket(session()->pull('last_called_ticket'));
        }
        $this->refreshAll();
        
    }

    public function refreshAll()
    {
        if (session()->has('last_called_ticket')) {
            $ticketId = session()->pull('last_called_ticket'); // Gets and removes
            $this->onCallTicket($ticketId);
        }

        $this->activeVideo = Video::where('is_active', true)->first();

        $this->nextTickets = Ticket::with(['area', 'latestLlamada.escritorio'])
            ->where('estado', 'pendiente')
            ->orderBy('created_at')
            ->limit(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id'       => $ticket->id,
                    'numero'   => $this->getTicketNumber($ticket),
                    'area'     => [
                        'codigo_area' => $ticket->area ? $ticket->area->codigo_area : '---',
                    ],
                    'escritorio' => [
                        'nombre_escritorio' => $ticket->latestLlamada && $ticket->latestLlamada->escritorio
                            ? $ticket->latestLlamada->escritorio->nombre_escritorio
                            : 'Sin escritorio'
                    ],
                ];
            })
            ->toArray();

        // Fetch latest called ticket
        $lastCalled = Ticket::with(['area', 'latestLlamada.escritorio'])
            ->where('estado', 'llamado')
            ->latest('created_at')
            ->first();

        if ($lastCalled) {
            $this->calledTicket = [
                'id'     => $lastCalled->id,
                'numero' => $this->getTicketNumber($lastCalled),
                'area'   => $lastCalled->area ? [
                    'codigo_area' => $lastCalled->area->codigo_area,
                ] : null,
                'escritorio' => $lastCalled->latestLlamada && $lastCalled->latestLlamada->escritorio ? [
                    'nombre_escritorio' => $lastCalled->latestLlamada->escritorio->nombre_escritorio
                ] : [
                    'nombre_escritorio' => 'Sin escritorio'
                ],
            ];
        } else {
            $this->calledTicket = null;
        }
    }

    public $lastSpokenTicketId = null;

    public function onCallTicket(int $ticketId)
    {
        logger("[TvDisplay] onCallTicket START - Received ticket: {$ticketId}");
        
        $ticket = Ticket::with(['area', 'latestLlamada.escritorio'])->findOrFail($ticketId);
        logger("[TvDisplay] Ticket found: ".json_encode([
            'id' => $ticket->id,
            'numero' => $this->getTicketNumber($ticket),
            'area' => $ticket->area?->codigo_area
        ]));

        $this->calledTicket = [
            'id' => $ticket->id,
            'numero' => $this->getTicketNumber($ticket),
            'area' => $ticket->area ? ['codigo_area' => $ticket->area->codigo_area] : null,
            'escritorio' => [
                'nombre_escritorio' => $ticket->latestLlamada?->escritorio?->nombre_escritorio ?? 'Sin escritorio'
            ]
        ];

        if ($this->lastSpokenTicketId !== $ticketId) {
            logger("[TvDisplay] Dispatching speak-ticket for ticket: {$ticketId}");
            
            // Updated dispatch with named parameters
            $this->dispatch('speak-ticket', [
            'codigo_area' => $this->calledTicket['area']['codigo_area'] ?? '---',
            'code'        => $this->calledTicket['numero'],
            'desk'        => $this->calledTicket['escritorio']['nombre_escritorio'],
            ]);
            
            $this->lastSpokenTicketId = $ticketId;
        }
        
        logger("[TvDisplay] Refreshing all data");
        $this->refreshAll();
    }
    /**
     * Get the appropriate ticket number depending on whether it's an adulto mayor.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return string|int|null
     */
    protected function getTicketNumber($ticket)
    {
        return $ticket->es_adulto_mayor ? $ticket->numero_adulto_mayor : $ticket->numero;
    }

    public function render()
    {
        return view('livewire.tv-display')
            ->layout('components.layouts.tv');
    }
}

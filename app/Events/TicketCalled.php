<?php

// app/Events/TicketCalled.php
namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;   // ← correct import!
use Illuminate\Queue\SerializesModels;

class TicketCalled implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public int $ticketId;
    public string $areaCode;
    public string $code;
    public string $desk;

    public function __construct(int $ticketId)
    {
        $this->ticketId   = $ticketId;

        // Pull the other details right here:
        $ticket = Ticket::with(['area','latestLlamada.escritorio'])->findOrFail($ticketId);
        $this->areaCode = $ticket->area?->codigo_area  ?? '---';
        $this->code     = $ticket->es_adulto_mayor 
                          ? $ticket->numero_adulto_mayor 
                          : $ticket->numero;
        $this->desk     = $ticket->latestLlamada?->escritorio?->nombre_escritorio 
                          ?? 'Sin escritorio';
    }

    public function broadcastOn(): Channel
    {
        return new Channel('tv-display');
    }

    public function broadcastAs(): string
    {
        return 'TicketCalled';
    }

    public function broadcastWith(): array
    {
        // this is what the JS will receive as `data`
        return [
            'ticketId' => $this->ticketId,
            'areaCode' => $this->areaCode,
            'code'     => $this->code,
            'desk'     => $this->desk,
        ];
    }
}
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use App\Models\Ticket;
use App\Events\VideoSwitched as VideoSwitchedEvent;


class TvDisplay extends Component
{
    public $activeVideo;
    public $nextTickets = [];
    public $calledTicket = null;
    public $currentVideoId = null;
    public $videos;


    protected $listeners = [
        'call-ticket' => 'onCallTicket',
        'videoSwitched' => 'onVideoSwitched',
        'video-playing'  => 'onVideoPlaying',  // ← add this line

    ];

    public function mount()
    {
        $this->videos = Video::where('is_active', true)->orderBy('uploaded_at')->get();

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

        // Fetch the latest called ticket
        $lastCalled = Ticket::with(['area', 'latestLlamada.escritorio'])
                ->whereIn('estado',['llamado','atendido'])
            ->latest('created_at')
            ->first();

        // Set current called ticket (for the top section)
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

        // Get the last 6 called tickets, *excluding* the current one
        $this->nextTickets = Ticket::with(['area', 'latestLlamada.escritorio'])
            ->where('estado', 'atendido')
            ->where('id', '!=', $this->calledTicket['id'] ?? 0)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id'       => $ticket->id,
                    'numero'   => $this->getTicketNumber($ticket),
                    'area'     => [
                        'codigo_area' => $ticket->area?->codigo_area ?? '---',
                    ],
                    'escritorio' => [
                        'nombre_escritorio' => $ticket->latestLlamada?->escritorio?->nombre_escritorio ?? 'Sin escritorio'
                    ],
                ];
            })
            ->toArray();
    }

    public $lastSpokenTicketId = null;

    public function onCallTicket(int $ticketId)
    {
        logger("[TvDisplay] onCallTicket START - Received ticket: {$ticketId}");
        
        $ticket = Ticket::with(['area', 'latestLlamada.escritorio'])->findOrFail($ticketId);

        $this->calledTicket = [
            'id'      => $ticket->id,
            'numero'  => $this->getTicketNumber($ticket),
            'area'    => ['codigo_area' => $ticket->area?->codigo_area ?? '---'],
            'escritorio' => ['nombre_escritorio' => $ticket->latestLlamada?->escritorio?->nombre_escritorio ?? 'Sin escritorio'],
        ];

        if ($this->lastSpokenTicketId !== $ticketId) {
            logger("[TvDisplay] Dispatching speak-ticket for ticket: {$ticketId}");
            $this->dispatch('speak-ticket', [
                'codigo_area' => $this->calledTicket['area']['codigo_area'],
                'code'        => $this->calledTicket['numero'],
                'desk'        => $this->calledTicket['escritorio']['nombre_escritorio'],
            ]);
            $this->lastSpokenTicketId = $ticketId;
        }
        
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
        $videos = Video::orderBy('id')->get();

        logger('Rendering TV view with videos:', $videos->toArray());


        return view('livewire.tv-display', [
            'videos' => $videos, // Always pass this collection directly
        ])->layout('components.layouts.tv');
        
    }

    public function onVideoSwitched(int $videoId)
    {
        // 0) Log entry
        logger("[TvDisplay] 🔄 onVideoSwitched fired with ID: {$videoId}");

        // 1) Clear the old “active” flag
        Video::query()->update(['is_active' => false]);
        logger("[TvDisplay]   • Cleared all is_active flags");

        // 2) Activate the one we just switched to
        Video::where('id', $videoId)->update(['is_active' => true]);
        logger("[TvDisplay]   • Activated video ID {$videoId}");

        // 3) Broadcast to any other TVs/admin panels
        broadcast(new VideoSwitchedEvent($videoId))->toOthers();
        logger("[TvDisplay]   • Broadcasted VideoSwitchedEvent");

        // 4) Tell your Alpine playlistData DIV to re‑read
        $this->dispatch('playlist-updated');
        logger("[TvDisplay]   • Dispatched playlist-updated browser event");
    }

    public function refreshVideos()
    {
        $this->videos = Video::where('is_active', true)->orderBy('uploaded_at')->get();
    }

    public function onVideoPlaying(int $videoId)
    {
        // 1) Clear any previous active flag
        Video::query()->update(['is_active' => false]);

        // 2) Mark the new one as active
        Video::where('id', $videoId)->update(['is_active' => true]);

        // 3) Broadcast the switch to any listening clients (including your admin view)
        broadcast(new VideoSwitchedEvent($videoId))->toOthers();
    }

}

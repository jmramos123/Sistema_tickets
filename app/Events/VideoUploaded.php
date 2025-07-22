<?php
namespace App\Events;

// app/Events/VideoUploaded.php

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\Video;

class VideoUploaded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function broadcastOn(): Channel
    {
        // 🔑 return a Channel instance, not an array
        return new Channel('tv-display');
    }

    public function broadcastAs(): string
    {
        return 'NewVideoUploaded';
    }

    public function broadcastWith(): array
    {
        return [
            'video' => [
                'id'            => $this->video->id,
                'ruta_archivo'  => $this->video->ruta_archivo,
                'nombre'        => $this->video->nombre,
                // optionally url if you need it client‑side:
                'url'           => asset('storage/' . $this->video->ruta_archivo),
            ],
        ];
    }
}

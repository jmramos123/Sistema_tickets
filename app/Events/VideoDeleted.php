<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;   // ← correct import!

class VideoDeleted implements ShouldBroadcastNow
{
    use InteractsWithSockets;

    public $videoId;

    public function __construct($videoId)
    {
        $this->videoId = $videoId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('tv-display');
    }

    public function broadcastAs(): string
    {
        return 'VideoDeleted';
    }
}

<?php

// app/Events/VideoSwitched.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class VideoSwitched implements ShouldBroadcastNow
{
    use SerializesModels;

    public int $videoId;

    public function __construct(int $videoId)
    {
        $this->videoId = $videoId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('tv-display');
    }

    public function broadcastAs(): string
    {
        return 'VideoSwitched';
    }
}

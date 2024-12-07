<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MinetopiaEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type;
    public $data;
    public $uuid;

    public function __construct(string $type, array $data, string $uuid)
    {
        $this->type = $type;
        $this->data = $data;
        $this->uuid = $uuid;
    }

    public function broadcastOn()
    {
        return new Channel('player.' . $this->uuid);
    }

    public function broadcastAs()
    {
        return $this->type;
    }
} 
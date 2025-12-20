<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class CallSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $receiverId;

    public function __construct(array $data, int $receiverId)
    {
        $this->data = $data;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('call.' . $this->receiverId)];
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}

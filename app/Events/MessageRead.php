<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageIds;
    public $readerId;
    public $senderId;

    public function __construct(array $messageIds, int $readerId, int $senderId)
    {
        $this->messageIds = $messageIds;
        $this->readerId = $readerId;
        $this->senderId = $senderId;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('chat.' . $this->senderId)];
    }

    public function broadcastWith(): array
    {
        return [
            'message_ids' => $this->messageIds,
            'reader_id' => $this->readerId,
        ];
    }
}

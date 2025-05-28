<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $username;
    public $message;
    public $senderId;

    public function __construct(int $senderId, string $username, string $message)
    {
        $this->senderId = $senderId;
        $this->username = $username;
        $this->message = $message;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('messages');
    }
}

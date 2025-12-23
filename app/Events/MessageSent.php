<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Message;
use App\Models\Conversation;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        $conv = $this->message->conversation()->with('participants:id')->first();

        return $conv->participants
            ->pluck('id')
            ->map(fn ($id) => new PrivateChannel('inbox.' . $id))
            ->all();
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
        echo "test";
    }
}

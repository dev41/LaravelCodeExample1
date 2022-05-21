<?php

namespace App\Events;

use App\Models\Chat;
use App\Transformers\ChatTransformer;
use App\Transformers\ChatUpdatedTransformer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class MessagesUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    private $chatMemberId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $fractal = app(Manager::class);
        $resource = new Item($chat, new ChatUpdatedTransformer());
        $this->chat = $fractal->createData($resource)->toArray();

        $this->chatMemberId = $chat->from_id;
        if ($this->chatMemberId == auth()->id()) {
            $this->chatMemberId = $chat->to_id;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel("messages-updated.{$this->chatMemberId}");
    }

    public function broadcastAs()
    {
        return 'messages-updated';
    }
}

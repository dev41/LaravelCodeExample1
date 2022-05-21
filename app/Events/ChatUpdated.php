<?php

namespace App\Events;

use App\Models\Chat;
use App\Repositories\Contracts\ChatsRepositoryInterface;
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

class ChatUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $totalUnreadChatsCount;
    private $chatFromId;
    private $chatToId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $fractal = app(Manager::class);
        $fractal->parseIncludes('members');
        $resource = new Item($chat, new ChatUpdatedTransformer());
        $this->chat = $fractal->createData($resource)->toArray();

        $this->chatFromId = $chat->from_id;
        $this->chatToId = $chat->to_id;

        $chatsRepository = app(ChatsRepositoryInterface::class);
        $this->totalUnreadChatsCount = [
            $this->chatFromId => $chatsRepository->getUserUnreadChatsCount($this->chatFromId),
            $this->chatToId => $chatsRepository->getUserUnreadChatsCount($this->chatToId)
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PresenceChannel("chats-list.{$this->chatFromId}"),
            new PresenceChannel("chats-list.{$this->chatToId}"),
//            new PresenceChannel("messages-updated.{$this->chatMemberId}")
        ];
    }

    public function broadcastAs()
    {
        return 'chat-updated';
    }
}

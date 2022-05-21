<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\Message;
use App\Repositories\Contracts\ChatsRepositoryInterface;
use App\Transformers\ChatUpdatedTransformer;
use App\Transformers\MessageTransformer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class ChatMessageDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $message;
    public $totalUnreadChatsCount;

    private $chatFromId;
    private $chatToId;
    private $chatId;

    /**
     * ChatMessageDeleted constructor.
     * @param Message $message
     * @param Chat $chat
     */
    public function __construct(Message $message, Chat $chat)
    {
        $fractal = app(Manager::class);
        $resource = new Item($message, new MessageTransformer());
        $this->message = $fractal->createData($resource)->toArray();

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

        $this->chatId = $chat->id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PresenceChannel("chat.{$this->chatId}"),
            new PresenceChannel("chats-list.{$this->chatFromId}"),
            new PresenceChannel("chats-list.{$this->chatToId}"),
        ];
    }

    public function broadcastAs()
    {
        return 'message-deleted';
    }
}

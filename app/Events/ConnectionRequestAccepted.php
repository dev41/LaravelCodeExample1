<?php

namespace App\Events;

use App\Models\Friend;
use App\Transformers\NewUserTransformer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class ConnectionRequestAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $user;
    private $userId;

    /**
     * ConnectionRequestSent constructor.
     * @param Friend $friendRequest
     */
    public function __construct(Friend $friendRequest)
    {
        $this->userId = $friendRequest->friend_id;
        if ($this->userId == auth()->id()) {
            $this->userId = $friendRequest->user_id;
        }

        $fractal = app(Manager::class);
        $resource = new Item(auth()->user(), new NewUserTransformer());
        $this->user = $fractal->createData($resource)->toArray();
    }

    public function broadcastOn()
    {
        return new PresenceChannel("notifications.{$this->userId}");
    }

    public function broadcastAs()
    {
        return 'accepted-user-connection-request';
    }
}

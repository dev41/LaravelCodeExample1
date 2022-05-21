<?php

namespace App\Events;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Transformers\NotificationTransformer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class NotificationAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $total_unread;
    private $userId;

    /**
     * NotificationAdded constructor.
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->userId = $notification->to_id;

        $notificationsRepository = app(NotificationsRepositoryInterface::class);
        $this->total_unread = $notificationsRepository->getCountByUserAndReadStatus($notification->to, Notification::IS_NOT_VIEWED);

        $fractal = app(Manager::class);
        $fractal->parseIncludes('object.group,object.group.member_count');
        $resource = new Item($notification, new NotificationTransformer());
        $this->notification = $fractal->createData($resource)->toArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel("notifications.{$this->userId}");
    }

    public function broadcastAs()
    {
        return 'notification-added';
    }
}

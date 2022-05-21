<?php

namespace App\Events;

use App\Helpers\AzureBlob;
use App\Models\Friend;
use App\Transformers\NewUserTransformer;
use App\Transformers\UserFriendTransformer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class ConnectionRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    private $user;
    private $userId;

    /**
     * ConnectionRequestSent constructor.
     * @param Friend $friendRequest
     */
    public function __construct(Friend $friendRequest)
    {
        $this->user = $friendRequest->friend;
        if ($this->user->id != auth()->id()) {
            $this->user = $friendRequest->user;
        }

        $this->userId = $friendRequest->friend_id;
        if ($this->userId == auth()->id()) {
            $this->userId = $friendRequest->user_id;
        }

        if ($this->user->profile_image != "" && auth()->user()->can('viewAvatar', $this->user)) {
            $profileImage = AzureBlob::url(config('constants.files.all_images_path') . $this->user->profile_image);
        } else {
            $profileImage = AzureBlob::url(config("constants.images.default_{$this->user->gender}_user_image"));
        }

        $this->request = [
            'id' => $friendRequest->id,
            'user_id' => (int)$friendRequest->user_id,
            'friend_id' => (int)$friendRequest->friend_id,
            'request_type' => (int)$friendRequest->request_type,
            'cdate' => $friendRequest->cdate,
            'name' => $this->user->name,
            'profile_image' => $profileImage,
            'display_name' => $this->user->display_name,
            'uid' => (int)$this->user->id,
            'is_online' => $this->user->isOnline()
        ];
    }

    public function broadcastOn()
    {
        return new PresenceChannel("notifications.{$this->userId}");
    }

    public function broadcastAs()
    {
        return 'new-connection-request';
    }
}

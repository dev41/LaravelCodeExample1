<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteGroupNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $groupId;

    /**
     * DeleteGroupNotifications constructor.
     * @param int $groupId
     */
    public function __construct(int $groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @param NotificationsRepositoryInterface $notificationsRepository
     * @return mixed
     */
    public function handle( NotificationsRepositoryInterface $notificationsRepository )
    {
        return $notificationsRepository->deleteGroupNotifications($this->groupId);
    }
}

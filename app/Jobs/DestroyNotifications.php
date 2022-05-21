<?php

namespace App\Jobs;

use App\Repositories\Contracts\NotificationsRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DestroyNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notifications;
    protected $tableName;

    /**
     * DestroyGroupNotifications constructor.
     * @param $notifications
     * @param $tableName
     */
    public function __construct($notifications, $tableName)
    {
        $this->notifications = $notifications;
        $this->tableName = $tableName;
    }

    /**
     * @param NotificationsRepositoryInterface $notificationsRepository
     * @return mixed
     */
    public function handle(NotificationsRepositoryInterface $notificationsRepository)
    {
        return $notificationsRepository->destroyByTableIdAndTableName($this->notifications, $this->tableName);
    }
}

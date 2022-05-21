<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteHubNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $hub;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($hub)
    {
        $this->hub = $hub;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( NotificationsRepositoryInterface $notificationsRepository )
    {
        return $notificationsRepository->deleteByType([
            Notification::TYPE_HUB_REQUEST_ACCEPTED,
            Notification::TYPE_HUB_REQUEST_REJECTED,
            Notification::TYPE_HUB_REQUEST_RECEIVED,
            Notification::TYPE_NEW_USER_ON_HUB], $this->hub);
    }
}

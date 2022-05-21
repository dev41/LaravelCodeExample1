<?php

namespace App\Jobs;

use App\Events\NotificationAdded;
use App\Models\Notification;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Notification as NotificationClass;
use Illuminate\Support\Facades\Log;

class AddNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;

    /**
     * AddNotification constructor.
     * @param $notification
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $notification = Notification::create($this->notification);

        if ($notification) {
            event(new NotificationAdded($notification));

            $userRepository = app(UserRepositoryInterface::class);
            if ($user = $userRepository->getByKey($notification->to_id)) {
                if (auth()->user()->can('receivePushNotifications', $user)) {
                    try {
                        $user->notify(new NotificationClass($notification));
                    } catch (\Exception $exception) {
                        Log::warning($exception);
                    }
                }
            }
        }

        return $notification;
    }
}

<?php

namespace App\Notifications;

use App\Helpers\OneSignalMessage;
use App\Repositories\Contracts\ChatsRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use App\Models\Notification as NotificationModel;

class UpdateOneSignalBadge extends Notification implements ShouldQueue
{
    use Queueable;

    public $timeout = 180;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->queue = 'one-signal';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toOneSignal($notifiable)
    {
        $chatsRepository = app(ChatsRepositoryInterface::class);
        $unreadMessagesCount = (int)$chatsRepository->getUserUnreadChatsCount($notifiable->id);

        $notificationsRepository = app(NotificationsRepositoryInterface::class);
        $unreadNotificationsCount = (int)$notificationsRepository->getCountByUserAndReadStatus(
            $notifiable,
            NotificationModel::IS_NOT_VIEWED
        );

        return OneSignalMessage::create()
            ->setData('type_id', NotificationModel::PUSH_NOTIFICATION_TYPE_BADGE_UPDATE)
            ->setParameter('content_available', true)
            ->setParameter('ios_badgeType', 'SetTo')
            ->setParameter('ios_badgeCount', $unreadMessagesCount + $unreadNotificationsCount)
            ->subject('')
            ->body('');
    }
}

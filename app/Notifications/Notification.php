<?php

namespace App\Notifications;

use App\Helpers\AzureBlob;
use App\Repositories\Contracts\ChatsRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Transformers\GroupTransformer;
use App\Transformers\HubTransformer;
use App\Transformers\NewUserTransformer;
use App\Transformers\PostTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as NotificationMain;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use App\Models\Notification as NotificationModel;

class Notification extends NotificationMain
{
    public $notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(NotificationModel $notification)
    {
        $this->notification = $notification;
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
        $fractal = app(Manager::class);

        $target = new \stdClass();

        if ($this->notification->object_id) {
            if (!auth()->check()) {
                auth()->login($this->notification->from);
            }

            switch ($this->notification->type) {
                case NotificationModel::OBJECT_TYPE_USER : $target = new Item($this->notification->user, new NewUserTransformer()); break;
                case NotificationModel::OBJECT_TYPE_FRIEND : $target = new Item($this->notification->friend, new NewUserTransformer()); break;
                case NotificationModel::OBJECT_TYPE_POST : {
                    $fractal->parseExcludes('comments,images,files,object');
                    $target = new Item($this->notification->post, new PostTransformer());
                } break;
                case NotificationModel::OBJECT_TYPE_GROUP : $target = new Item($this->notification->group, new GroupTransformer()); break;
                case NotificationModel::OBJECT_TYPE_HUB : $target = new Item($this->notification->hub, new HubTransformer()); break;
            }
        }

        $chatsRepository = app(ChatsRepositoryInterface::class);
        $unreadMessagesCount = (int)$chatsRepository->getUserUnreadChatsCount($notifiable->id);

        $notificationsRepository = app(NotificationsRepositoryInterface::class);
        $unreadNotificationsCount = (int)$notificationsRepository->getCountByUserAndReadStatus($notifiable, NotificationModel::IS_NOT_VIEWED);

        return OneSignalMessage::create()
            ->setData('type_id', $this->notification->notification_type)
            ->setData('notification_id', $this->notification->id)
            ->setData('target', $fractal->createData($target)->toArray())
            ->setParameter('content_available', true)
            ->setParameter('ios_badgeType', 'SetTo')
            ->setParameter('ios_badgeCount', $unreadMessagesCount + $unreadNotificationsCount)
            ->subject("New Notification")
            ->body(
                ($this->notification->type == NotificationModel::OBJECT_TYPE_FRIEND || $this->notification->type == NotificationModel::OBJECT_TYPE_POST) ? ("{$this->notification->from->name} {$this->notification->message}") : $this->notification->message
            )
            ->icon($this->notification->from->getImage());
    }
}

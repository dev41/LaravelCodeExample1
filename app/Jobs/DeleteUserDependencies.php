<?php

namespace App\Jobs;

use App\Repositories\Contracts\ChatsRepositoryInterface;
use App\Repositories\Contracts\FriendsRepositoryInterface;
use App\Repositories\Contracts\GroupConnectionsRepositoryInterface;
use App\Repositories\Contracts\GroupRequestsRepositoryInterface;
use App\Repositories\Contracts\IgnoresRepositoryInterface;
use App\Repositories\Contracts\ImagesRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Repositories\Contracts\OffNotificationsRepositoryInterface;
use App\Repositories\Contracts\PasswordResetsRepositoryInterface;
use App\Repositories\Contracts\PostBlocksRepositoryInterface;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteUserDependencies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * DeleteUserDependencies constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param FriendsRepositoryInterface $friendsRepository
     * @param GroupConnectionsRepositoryInterface $groupConnectionsRepository
     * @param GroupRequestsRepositoryInterface $groupRequestsRepository
     * @param IgnoresRepositoryInterface $ignoresRepository
     * @param ImagesRepositoryInterface $imagesRepository
     * @param NotificationsRepositoryInterface $notificationsRepository
     * @param OffNotificationsRepositoryInterface $offNotificationsRepository
     * @param PasswordResetsRepositoryInterface $passwordResetsRepository
     * @param PostBlocksRepositoryInterface $postBlocksRepository
     * @param ChatsRepositoryInterface $chatsRepository
     */
    public function handle(
        FriendsRepositoryInterface $friendsRepository,
        GroupConnectionsRepositoryInterface $groupConnectionsRepository,
        GroupRequestsRepositoryInterface $groupRequestsRepository,
        IgnoresRepositoryInterface $ignoresRepository,
        ImagesRepositoryInterface $imagesRepository,
        NotificationsRepositoryInterface $notificationsRepository,
        OffNotificationsRepositoryInterface $offNotificationsRepository,
        PasswordResetsRepositoryInterface $passwordResetsRepository,
        PostBlocksRepositoryInterface $postBlocksRepository,
        ChatsRepositoryInterface $chatsRepository
    ) {
        $friendsRepository->deleteAllUserFriends($this->user);

        $groupConnectionsRepository->delete(['user_id' => $this->user->id]);
        $groupRequestsRepository->delete(['user_id' => $this->user->id]);
        $groupRequestsRepository->delete(['invited_by' => $this->user->id]);
        if ($this->user->groups->isNotEmpty()) {
            foreach ($this->user->groups as $group) {
                $group->delete();
            }
        }

        if ($this->user->hubs->isNotEmpty()) {
            foreach ($this->user->hubs as $hub) {
                $hub->delete();
            }
        }

        $ignoresRepository->delete(['user_id' => $this->user->id]);

        $imagesRepository->delete(['user_id' => $this->user->id]);

        if ($this->user->files->isNotEmpty()) {
            foreach ($this->user->files as $file) {
                $file->delete();
            }
        }

        $this->user->oneSignalPlayers()->delete();

        $this->user->articles()->delete();

        $this->user->events()->delete();

        $notificationsRepository->deleteAllUserNotifications($this->user);
        $offNotificationsRepository->delete(['user_id' => $this->user->id]);

        $passwordResetsRepository->delete(['user_id' => $this->user->id]);

        $postBlocksRepository->delete(['user_id' => $this->user->id]);
        if ($this->user->posts->isNotEmpty()) {
            foreach ($this->user->posts as $post) {
                $post->delete();
            }
        }

        $this->user->privacySettings()->delete();
        $this->user->emailNotificationsSettings()->delete();

        $chats = $chatsRepository->getAllUserChats($this->user);
        if ($chats->isNotEmpty()) {
            foreach ($chats as $chat) {
                if ($chat->messages->isNotEmpty()) {
                    foreach ($chat->messages as $message) {
                        $message->delete();
                    }
                }
                $chat->delete();
            }
        }

        $this->user->comments()->delete();
    }
}

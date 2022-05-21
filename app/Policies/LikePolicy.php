<?php

namespace App\Policies;

use App\Models\PostLike;
use App\Repositories\Contracts\FriendsRepositoryInterface;
use App\Repositories\Contracts\OffNotificationsRepositoryInterface;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LikePolicy
{
    use HandlesAuthorization;

    public function view(User $viewer, PostLike $like)
    {
        if (optional($like->user)->is_active == User::NOT_ACTIVE) {
            return false;
        }

        if (optional($like->user)->is_deleted == User::DELETED) {
            return false;
        }

        return true;
    }

    public function receivePostLikeNotification(User $viewer, PostLike $like)
    {
        if ($like->type != PostLike::TYPE_POST_LIKE) {
            return false;
        }

        if ($viewer->id == auth()->id()) {
            return false;
        }

        $offNotificationsRepository = app(OffNotificationsRepositoryInterface::class);
        if ($offNotificationsRepository->getByUserAndPost($viewer, $like->post)) {
            return false;
        }

        $friendsRepository = app(FriendsRepositoryInterface::class);
        if (!$friendsRepository->getByUserIdAndFriendId($viewer->id, $like->user->id)) {
            return false;
        }

        return true;
    }
}

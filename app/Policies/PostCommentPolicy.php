<?php

namespace App\Policies;

use App\Models\PostComment;
use App\Repositories\Contracts\FriendsRepositoryInterface;
use App\Repositories\Contracts\OffNotificationsRepositoryInterface;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostCommentPolicy
{
    use HandlesAuthorization;

    public function delete(User $viewer, PostComment $comment)
    {
        if ($viewer->id == optional($comment->user)->id) {
            return true;
        }

        return false;
    }

    public function view(User $viewer, PostComment $comment)
    {
        if ($comment->user->is_active == User::NOT_ACTIVE) {
            return false;
        }

        if ($comment->user->is_deleted == User::DELETED) {
            return false;
        }

        if ($comment->is_blocked == PostComment::IS_BLOCKED) {
            return false;
        }

        if ($comment->is_delete == PostComment::IS_DELETED) {
            return false;
        }

        if ($viewer->commentIgnores->contains('comment_id', $comment->id)) {
            return false;
        }

        return true;
    }

    public function receivePostCommentNotification(User $viewer, PostComment $comment)
    {
        if ($viewer->id == auth()->id()) {
            return false;
        }

        $offNotificationsRepository = app(OffNotificationsRepositoryInterface::class);
        if ($offNotificationsRepository->getByUserAndPost($viewer, $comment->post)) {
            return false;
        }

        $friendsRepository = app(FriendsRepositoryInterface::class);
        if (!$friendsRepository->getByUserIdAndFriendId($viewer->id, $comment->user->id)) {
            return false;
        }

        return true;
    }
}

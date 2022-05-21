<?php

namespace App\Policies;

use App\Models\Message;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function delete(User $viewer, Message $message)
    {
        if (in_array($viewer->id, [$message->chat->from_id, $message->chat->to_id])) {
            return true;
        }

        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Hub;
use App\Models\HubInvite;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HubPolicy
{
    use HandlesAuthorization;

    public function delete(User $viewer, Hub $hub)
    {
        return $viewer->id == $hub->user->id;
    }

    public function leave(User $viewer, Hub $hub)
    {
        return $viewer->id != $hub->user->id;
    }

    public function viewInvites(User $viewer, Hub $hub)
    {
        return $viewer->id == $hub->user->id;
    }

    public function update(User $viewer, Hub $hub)
    {
        return $viewer->id == $hub->user->id;
    }

    public function view(User $viewer, Hub $hub)
    {
        if ($hub->privacy == Hub::PRIVACY_PRIVATE) {
            return $hub->invites->where('status', HubInvite::STATUS_ACCEPTED)->contains('user_id', $viewer->id);
        }

        return true;
    }
}

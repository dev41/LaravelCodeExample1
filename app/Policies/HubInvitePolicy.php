<?php

namespace App\Policies;

use App\Models\Hub;
use App\Models\HubInvite;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HubInvitePolicy
{
    use HandlesAuthorization;

    public function accept(User $viewer, HubInvite $invite)
    {
        if ($invite->user_id == $viewer->id) {
            return true;
        }

        if ($invite->hub->user_id == $viewer->id
            && $invite->hub->privacy == Hub::PRIVACY_PRIVATE
            && $invite->is_request = HubInvite::IS_REQUEST
        ) {
            return true;
        }

        return false;
    }

    public function reject(User $viewer, HubInvite $invite)
    {
        if ($invite->user_id == $viewer->id) {
            return true;
        }

        if ($invite->hub->user_id == $viewer->id
            && $invite->hub->privacy == Hub::PRIVACY_PRIVATE
            && $invite->is_request = HubInvite::IS_REQUEST
        ) {
            return true;
        }

        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\GroupRequest;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupRequestPolicy
{
    use HandlesAuthorization;

    public function accept(User $viewer, GroupRequest $request)
    {
        if ($viewer->id == $request->user_id
            && in_array($request->request_type, [0, GroupRequest::TYPE_REQUEST_PENDING])
        ) {
            return true;
        }

        if ($viewer->id == $request->group->user->id
            && $request->group->group_type == Group::GROUP_TYPE_PRIVATE
            && !$request->invited_by
            && in_array($request->request_type, [0, GroupRequest::TYPE_REQUEST_PENDING])
        ) {
            return true;
        }

        return false;
    }

    public function reject(User $viewer, GroupRequest $request)
    {
        if ($request->user_id == $viewer->id
            && in_array($request->request_type, [0, GroupRequest::TYPE_REQUEST_PENDING])
        ) {
            return true;
        }

        if ($viewer->id == $request->group->user->id
            && $request->group->group_type == Group::GROUP_TYPE_PRIVATE
            && !$request->invited_by
            && in_array($request->request_type, [0, GroupRequest::TYPE_REQUEST_PENDING])
        ) {
            return true;
        }

        return false;
    }
}

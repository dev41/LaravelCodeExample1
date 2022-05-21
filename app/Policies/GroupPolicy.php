<?php

namespace App\Policies;

use App\Models\Group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    public function view(User $viewer, Group $group)
    {
        if ($group->group_type == Group::GROUP_TYPE_PRIVATE) {
            return $group->connections->contains('user_id', $viewer->id);
        }

        return true;
    }

    public function delete(User $viewer, Group $group)
    {
        return $group->user->id == $viewer->id;
    }

    public function addAdmin(User $viewer, Group $group)
    {
        return $viewer->id == $group->user->id || $group->admins->contains('user_id', $viewer->id);
    }

    public function deleteAdmin(User $viewer, Group $group)
    {
        return $viewer->id == $group->user->id || $group->admins->contains('user_id', $viewer->id);
    }

    public function update(User $viewer, Group $group)
    {
        return $viewer->id == $group->user->id || $group->admins->contains('user_id', $viewer->id);
    }

    public function deleteGroupMember(User $viewer, Group $group)
    {
        return $viewer->id == $group->user->id || $group->admins->contains('user_id', $viewer->id);
    }
}

<?php

namespace App\Policies;

use App\Models\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function delete(User $viewer, Post $post)
    {
        if ($viewer->id == optional($post->user)->id) {
            return true;
        }

        return false;
    }
}

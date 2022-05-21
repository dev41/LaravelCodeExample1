<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class PostsRepository implements PostsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    public function getByIdAndUserId($postId, $userId)
    {
        return $this->model->where([
            'id' => $postId,
            'user_id' => $userId
        ])->first();
    }

    public function getPostsForUser(array $conditions)
    {
        $query = $this->model
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->select([
                'posts.*'
            ])
            ->where(function ($query) use ($conditions) {
                $query->where(function ($query) use ($conditions) {
                        $query->where('posts.user_id', $conditions['currentUserId'])
                            ->whereNotIn('posts.group_type', [
                                Post::GROUP_TYPE_GROUP_POST,
                                Post::GROUP_TYPE_HUB_POST
                            ]);
                    })
                    ->orWhere(function ($query) use ($conditions) {
                        $query->where('posts.group_type', Post::GROUP_TYPE_GROUP_POST)
                            ->whereIn('posts.group_id', $conditions['userGroupsIds']);
                    })
                    ->orWhere(function ($query) use ($conditions) {
                        $query->where('posts.group_type', Post::GROUP_TYPE_HUB_POST)
                            ->whereIn('posts.group_id', $conditions['userHubsIds']);
                    })
                    ->orWhere(function ($query) use ($conditions) {
                        $query->where('posts.post_type', Post::TYPE_WITH_CONNECTIONS)
                            ->whereIn('posts.user_id', $conditions['userFriendsIds'])
                            ->whereNotIn('posts.group_type', [
                                Post::GROUP_TYPE_GROUP_POST,
                                Post::GROUP_TYPE_HUB_POST
                            ]);
                    })
                    ->orWhere(function ($query) use ($conditions) {
                        $query->whereIn('posts.post_type', [Post::TYPE_PUBLIC, Post::TYPE_ANONYMOUS])
                            ->whereNotIn('posts.group_type', [
                                Post::GROUP_TYPE_GROUP_POST,
                                Post::GROUP_TYPE_HUB_POST
                            ]);
                    });
            })
            ->whereNotIn('posts.id', $conditions['ignoredPostsIds'])
            ->where('posts.group_type', '<>', Post::GROUP_TYPE_ANOTHER_USER)
            ->where([
                'posts.is_active' => Post::STATUS_ACTIVE,
                'posts.is_blocked' => Post::IS_NOT_BLOCKED,
                'users.is_active' => User::ACTIVE
            ]);

        if (isset($conditions['lastPostCreatedDate'])) {
            if (isset($conditions['postsType']) && $conditions['postsType'] == 'newer') {
                $query->where('posts.created_date', '>', $conditions['lastPostCreatedDate']);
            } else {
                $query->where('posts.created_date', '<', $conditions['lastPostCreatedDate']);
            }
        }

        if (isset($conditions['offset']) && isset($conditions['itemsPerPage']) && ((isset($conditions['postsType']) && $conditions['postsType'] != 'newer') || !isset($conditions['postsType']))) {
            $query->offset($conditions['offset'])
                ->take($conditions['itemsPerPage']);
        }

        return $query->groupBy('posts.id')
            ->distinct()
            ->orderBy('created_date', 'DESC')
            ->get();
    }

    public function getPostsForUserProfile(array $conditions)
    {
        $query = $this->model
            ->where(function ($query) use ($conditions) {
                $query->where(function ($query) use ($conditions) {
                    $query->where('user_id', $conditions['currentUserId'])
                        ->whereNotIn('group_type', [
                            Post::GROUP_TYPE_GROUP_POST,
                            Post::GROUP_TYPE_COMPANY_POST,
                            Post::GROUP_TYPE_HUB_POST,
                            Post::GROUP_TYPE_ANOTHER_USER
                        ])
                        ->where(function ($query) use ($conditions) {
                            $query->where(function ($query) use ($conditions) {
                                $query->where('post_type', Post::TYPE_WITH_CONNECTIONS)
                                    ->whereExists(function ($query) use ($conditions) {
                                        $query->select('friends.id')
                                            ->from('friends')
                                            ->where(function ($query) use ($conditions) {
                                                $query->orWhere(function ($query) {
                                                    $query->whereRaw('posts.user_id = friends.user_id')
                                                        ->where('friends.friend_id', auth()->id());
                                                })
                                                ->orWhere(function ($query) {
                                                    $query->whereRaw('posts.user_id = friends.friend_id')
                                                        ->where('friends.user_id', auth()->id());
                                                })
                                                ->orWhere('posts.user_id', auth()->id());
                                            });
                                    });
                                })
                            ->orWhere('post_type', Post::TYPE_PUBLIC)
                            ->orWhere(function ($query) use ($conditions) {
                                $query->where([
                                    'post_type' => Post::TYPE_ANONYMOUS,
                                    'user_id' => auth()->id()
                                ]);
                            });
                        });
                })
                ->orWhere(function ($query) use ($conditions) {
                    $query->where([
                        'group_type' => Post::GROUP_TYPE_ANOTHER_USER,
                        'group_id' => $conditions['currentUserId']
                    ]);
                });
            })
            ->whereNotIn('id', $conditions['ignoredPostsIds']);

        if (isset($conditions['lastPostCreatedDate'])) {
            if (isset($conditions['postsType']) && $conditions['postsType'] == 'newer') {
                $query->where('created_date', '>', $conditions['lastPostCreatedDate']);
            } else {
                $query->where('created_date', '<', $conditions['lastPostCreatedDate']);
            }
        }

        if (isset($conditions['offset']) && isset($conditions['itemsPerPage']) && ((isset($conditions['postsType']) && $conditions['postsType'] != 'newer') || !isset($conditions['postsType']))) {
            $query->offset($conditions['offset'])
                ->take($conditions['itemsPerPage']);
        }

        return $query->notBlocked()
            ->active()
            ->groupBy('id')
            ->distinct()
            ->orderBy('created_date', 'DESC')
            ->get();
    }

    public function getAllGroupPosts(array $conditions)
    {
        $query = $this->model
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->select([
                'posts.*'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'posts.is_active' => Post::STATUS_ACTIVE,
                'posts.group_id' => $conditions['groupId'],
                'posts.group_type' => $conditions['groupType']
            ]);

        if (isset($conditions['ignoredPostsIds'])) {
            $query->whereNotIn('posts.id', $conditions['ignoredPostsIds']);
        }

        if (isset($conditions['lastPostCreatedDate'])) {
            if (isset($conditions['postsType'])) {
                if ($conditions['postsType'] == 'newer') {
                    $query->where('posts.created_date', '>', $conditions['lastPostCreatedDate']);
                } elseif ($conditions['postsType'] == 'older') {
                    $query->where('posts.created_date', '<', $conditions['lastPostCreatedDate']);
                }
            } else {
                $query->where('posts.created_date', '<', $conditions['lastPostCreatedDate']);
            }
        }

        $query->orderBy('posts.id', 'DESC');

        if (isset($conditions['count'])) {
            return $query->count();
        }

        if ((isset($conditions['postsType']) && $conditions['postsType'] != 'newer') || !isset($conditions['postsType'])) {
            return $query
                ->offset($conditions['offset'])
                ->take($conditions['itemsPerPage'])
                ->get();
        }


        return $query->get();
    }

    public function getByIdAndGroupId(int $postId, int $groupId)
    {
        return $this->model
            ->where([
                'id' => $postId,
                'group_id' => $groupId
            ])
            ->first();
    }

    public function getActiveById(int $postId)
    {
        return $this->model
            ->where([
                'id' => $postId,
                'is_active' => Post::STATUS_ACTIVE
            ])
            ->first();
    }
}

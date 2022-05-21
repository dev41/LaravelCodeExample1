<?php

namespace App\Repositories;

use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Settings;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class NotificationsRepository implements NotificationsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    public function destroyByTypeAndTablePIds($type, $tablePIds)
    {
        return $this->model->where([
            'notification_type' => $type
        ])
            ->whereIn('table_p_id', $tablePIds)
            ->delete();
    }

    public function getUserNotifications($user)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'notifications.from_id')
            ->join('friends', function ($join) {
                $join->on('users.id', '=', 'friends.user_id')
                    ->orOn('users.id', '=', 'friends.friend_id');
            })
            ->select([
                'notifications.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name', 'users.company_name', 'users.id AS uid'
            ])
            ->where([
                'notifications.to_id' => $user->id,
                'notifications.is_active' => Notification::IS_ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'users.is_active' => User::ACTIVE
            ])
            ->groupBy('notifications.id')
            ->orderBy('notifications.id', 'DESC')
            ->get();
    }

    public function getNotViewedUserNotifications($userId)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'notifications.from_id')
            ->select([
                'notifications.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name'
            ])
            ->where([
                'notifications.to_id' => $userId,
                'notifications.is_view' => Notification::IS_NOT_VIEWED,
                'notifications.is_active' => Notification::IS_ACTIVE,
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
            ])
            ->orderBy('notifications.id', 'DESC')
            ->get();
    }

    public function setViewedByIds($notificationsIds)
    {
        return $this->model
            ->whereIn('id', $notificationsIds)
            ->update([
                'is_view' => Notification::IS_VIEWED
            ]);
    }

    public function setUserNotificationsAsViewed($user)
    {
        return $this->model
            ->where([
                'to_id' => $user->id
            ])
            ->where(function ($query) {
                $query->where(['is_view' => Notification::IS_NOT_VIEWED])
                    ->orWhere(['is_read' => Notification::IS_NOT_READ])
                    ->orWhereNull('is_read')
                    ->orWhereNull('is_view');
            })
            ->update([
                'is_view' => Notification::IS_VIEWED,
                'is_read' => Notification::IS_READ
            ]);
    }

    public function destroyByTableIdAndTableName(array $tableIds, string $tableName)
    {
        return $this->model
            ->whereIn('table_p_id', $tableIds)
            ->where([
                'table_name' => $tableName
            ])
            ->delete();
    }

    public function deleteByType(array $types, int $tablePId)
    {
        return $this->model
            ->whereIn('notification_type', $types)
            ->where('table_p_id', $tablePId)
            ->delete();
    }

    public function getByUser(User $user, int $limit = 10)
    {
        return $this->model
            ->where([
                'to_id' => $user->id,
                'is_active' => Notification::IS_ACTIVE
            ])
            ->whereNotIn('from_id', [$user->id])
            ->orderBy('created_date', 'DESC')
            ->paginate($limit);
    }

    public function getByUserAndReadStatus(User $user, int $status, int $limit = 10)
    {
        $query = $this->model
            ->where([
                'to_id' => $user->id,
                'is_active' => Notification::IS_ACTIVE
            ]);
        if ($status == Notification::IS_NOT_READ) {
            $query->where(function ($query) use ($status) {
                $query->where(['is_read' => $status])
                    ->orWhereNull('is_read');
            });
        } else {
            $query->where(['is_read' => $status]);
        }

        return $query->whereNotIn('from_id', [$user->id])
            ->orderBy('created_date', 'DESC')
            ->paginate($limit);
    }

    public function getCountByUserAndReadStatus(User $user, int $isRead)
    {
        return $this->model
            ->where([
                'to_id' => $user->id,
                'is_active' => Notification::IS_ACTIVE,
                'is_view' => $isRead
            ])
            ->whereNotIn('from_id', [$user->id])
            ->count();
    }

    public function deleteAllUserNotifications(User $user)
    {
        return $this->model
            ->where('to_id', $user->id)
            ->orWhere('from_id', $user->id)
            ->delete();
    }

    public function deleteGroupNotifications(int $groupId)
    {
        return $this->model
            ->where('object_id', $groupId)
            ->where('type', Notification::OBJECT_TYPE_GROUP)
            ->delete();
    }

    public function deleteUserFriendNotifications(int $userId, int $friendId)
    {
        return $this->model
            ->where(function ($query) use ($userId, $friendId) {
                $query->where([
                    'from_id' => $userId,
                    'to_id' => $friendId
                ])
                 ->orWhere(function ($query) use ($userId, $friendId) {
                     $query->where(['from_id' => $friendId])
                         ->where(['to_id' => $userId]);
                 });
            })
            ->where('type', Notification::OBJECT_TYPE_FRIEND)
            ->delete();
    }
}
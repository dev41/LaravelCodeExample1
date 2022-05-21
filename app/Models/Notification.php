<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    const TYPE_LIKE_POST = 1;
    const TYPE_COMMENT_POST = 2;
    const TYPE_PROFILE_IMAGE_UPDATE = 3;
    const TYPE_COVER_IMAGE_UPDATE = 4;
    const TYPE_POST_ADDED = 5;
    const TYPE_SEND_FRIEND_REQUEST = 6;
    const TYPE_ACCEPTED_REQUEST = 7;
    const TYPE_REJECTED_REQUEST = 8;
    const TYPE_NEW_GROUP_REQUEST = 9;
    const TYPE_GROUP_REQUEST_ACCEPTED = 10;
    const TYPE_GROUP_REQUEST_REJECTED = 11;
    const TYPE_EMPLOYEE_ADDED = 12;
    const TYPE_SEND_REQUEST_TO_COMPANY = 13;
    const TYPE_REJECT_REQUEST_EMPLOYEE = 14;
    const TYPE_ACCEPT_REQUEST_EMPLOYEE = 15;
    const TYPE_FOLLOW_COMPANY = 16;
    const TYPE_UNFOLLOW_COMPANY = 17;
    const TYPE_PUBLIC_GROUP_JOIN = 18;
    const TYPE_REQUEST_TO_USER_FRIEND_JOIN_GROUP = 19;
    const TYPE_REJECTED_GRP_REQUEST_BY_USER = 20;
    const TYPE_ACCEPTED_GRP_REQUEST_BY_USER = 21;
    const TYPE_REFER_TO_THE_GROUP = 22;
    const TYPE_HUB_REQUEST_ACCEPTED = 23;
    const TYPE_HUB_REQUEST_REJECTED = 24;
    const TYPE_HUB_REQUEST_RECEIVED = 25;
    const TYPE_UNFLOW_COMPANY = 26;
    const TYPE_NEW_USER_ON_HUB = 27;
    const TYPE_LEAVE_GROUP = 28;
    const TYPE_COMPANY_JOIN_REQUEST = 29;

    const IS_NOT_VIEWED = 0;
    const IS_VIEWED = 1;

    const IS_NOT_READ = 0;
    const IS_READ = 1;

    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    const OBJECT_TYPE_USER = 'users';
    const OBJECT_TYPE_GROUP = 'groups';
    const OBJECT_TYPE_HUB = 'hubs';
    const OBJECT_TYPE_POST = 'posts';
    const OBJECT_TYPE_FRIEND = 'friends';

    const PUSH_NOTIFICATION_TYPE_NEW_MESSAGE = 35;
    const PUSH_NOTIFICATION_TYPE_BADGE_UPDATE = 36;

    protected $table = 'notifications';

    protected $fillable = [
        'from_id', 'to_id', 'notification_type', 'message', 'table_name', 'table_p_id', 'is_view', 'link',
        'is_active', 'created_date', 'object_slug', 'type', 'is_read', 'object_id'
    ];

    public $timestamps = false;

    public function from()
    {
        return $this->hasOne('App\User', 'id', 'from_id');
    }

    public function to()
    {
        return $this->hasOne('App\User', 'id', 'to_id');
    }

    public function groupRequest()
    {
        return $this->hasOne('App\Models\GroupRequest', 'id', 'table_p_id')->join('notifications', function ($join) {
            $join->on('notifications.table_p_id', '=', 'group_requests.id')
                ->whereIn('notifications.notification_type', [self::TYPE_REFER_TO_THE_GROUP, self::TYPE_REQUEST_TO_USER_FRIEND_JOIN_GROUP]);
        })->select(['group_requests.*']);
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'object_id')->join('notifications', function ($join) {
            $join->on('notifications.object_id', '=', 'users.id')
                ->where('notifications.type', self::OBJECT_TYPE_USER);
        })->select(['users.*']);
    }

    public function friend()
    {
        return $this->hasOne('App\User', 'id', 'object_id')->join('notifications', function ($join) {
            $join->on('notifications.object_id', '=', 'users.id')
                ->where('notifications.type', self::OBJECT_TYPE_FRIEND);
        })->select(['users.*']);
    }

    public function post()
    {
        return $this->hasOne('App\Models\Post', 'id', 'object_id')->join('notifications', function ($join) {
            $join->on('notifications.object_id', '=', 'posts.id')
                ->where('notifications.type', self::OBJECT_TYPE_POST);
        })->select(['posts.*']);
    }

    public function group()
    {
        return $this->hasOne('App\Models\Group', 'id', 'object_id')->join('notifications', function ($join) {
            $join->on('notifications.object_id', '=', 'groups.id')
                ->where('notifications.type', self::OBJECT_TYPE_GROUP);
        })->select(['groups.*']);
    }

    public function hub()
    {
        return $this->hasOne('App\Models\Hub', 'id', 'object_id')->join('notifications', function ($join) {
            $join->on('notifications.object_id', '=', 'hubs.id')
                ->where('notifications.type', self::OBJECT_TYPE_HUB);
        })->select(['hubs.*']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupRequest extends Model
{
    const TYPE_REQUEST_PENDING = 1;
    const TYPE_ACCEPTED = 2;
    const TYPE_REJECTED = 3;
    const TYPE_LEAVE_GROUP = 4;

    const IS_NOT_ADMIN_ANSWER = 0;
    const IS_ADMIN_ANSWER = 1;

    const IS_NOT_FRIEND_REQUEST = 0;
    const IS_FRIEND_REQUEST = 1;

    protected $table = 'group_requests';

    protected $fillable = [
        'user_id', 'group_id', 'request_type', 'is_admin_request', 'is_friend_request', 'cdate', 'invited_by'
    ];

    public $timestamps = false;

    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function inviter()
    {
        return $this->belongsTo('App\User', 'invited_by', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubInvite extends Model
{
    const STATUS_NOT_INVITED = 0;
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;

    const IS_NOT_REQUEST = 0;
    const IS_REQUEST = 1;

    protected $table = 'hub_invites';

    protected $fillable = [
        'hub_id', 'user_id', 'status', 'is_request', 'invited_by'
    ];

    public function hub()
    {
        return $this->belongsTo('App\Models\Hub', 'hub_id', 'id');
    }

    public function inviter()
    {
        return $this->belongsTo('App\User', 'invited_by', 'id');
    }

    public function invitedUser()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOneSignalPlayer extends Model
{
    protected $table = 'user_onesignal_players';

    protected $fillable = [
        'user_id', 'player_id'
    ];
}

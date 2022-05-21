<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffNotification extends Model
{
    protected $table = 'off_notifications';

    protected $fillable = [
        'user_id', 'post_id', 'notification_id'
    ];

    public $timestamps = false;
}

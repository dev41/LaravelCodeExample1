<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmailNotificationsSettings extends Model
{
    protected $table = 'user_email_notifications_settings';

    protected $fillable = [
        'user_id', 'setting_id'
    ];

    public function setting()
    {
        return $this->belongsTo('App\Models\EmailNotificationSettings', 'setting_id', 'id');
    }
}

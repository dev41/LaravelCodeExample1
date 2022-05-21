<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivacySettings extends Model
{
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;

    const NOT_DELETED = 0;
    const DELETED = 1;

    protected $table = 'privacy_settings';

    protected $fillable = [
        'settings_name', 'status', 'is_delete', 'c_date'
    ];

    public $timestamps = false;
}

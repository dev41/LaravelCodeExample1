<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mentorship extends Model
{
    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    const IS_NOT_DELETED = 0;
    const IS_DELETED = 1;

    protected $table = 'mentorships';

    protected $fillable = [
        'name', 'email', 'mobile_no', 'image', 'short_desc', 'long_desc', 'qualification', 'cdate', 'is_active',
        'is_deleted'
    ];

    public $timestamps = false;
}

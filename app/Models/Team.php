<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;

    const NOT_DELETED = 0;
    const DELETED = 1;

    protected $table = 'teams';

    protected $fillable = [
        'name', 'email', 'mobile_no', 'image', 'qualification', 'message', 'is_active', 'is_deleted', 'cdate',
        'is_admin'
    ];

    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    protected $table = 'subscribers';

    protected $fillable = [
        'email', 'user_name', 'date', 'is_active'
    ];

    public $timestamps = false;
}

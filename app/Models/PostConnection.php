<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostConnection extends Model
{
    protected $table = 'post_connections';

    protected $fillable = [
        'post_id', 'user_id', 'is_blocked', 'cdate'
    ];

    public $timestamps = false;
}

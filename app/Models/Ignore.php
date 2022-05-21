<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ignore extends Model
{
    protected $table = 'ignores';

    protected $fillable = [
        'post_id', 'comment_id', 'user_id'
    ];

    public $timestamps = false;
}

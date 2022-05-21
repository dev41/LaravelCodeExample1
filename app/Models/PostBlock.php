<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostBlock extends Model
{
    const GROUP_TYPE_SOCIAL_POST = 1;
    const GROUP_TYPE_GROUP_POST = 2;
    const GROUP_TYPE_COMPANY_POST = 3;
    const GROUP_TYPE_HUB_POST = 4;

    const TYPE_POST = 1;
    const TYPE_COMMENT = 2;

    const NOT_DELETED = 0;
    const DELETED = 1;

    protected $table = 'post_blocks';

    protected $fillable = [
        'group_id', 'user_id', 'group_type', 'post_id', 'is_delete', 'type'
    ];

    public $timestamps = false;
}

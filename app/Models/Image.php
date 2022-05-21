<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    const IMAGE_TYPE_NORMAL = 1;
    const IMAGE_TYPE_GROUP = 2;
    const IMAGE_TYPE_COMPANY = 3;

    const TYPE_PROFILE_IMAGE = 1;
    const TYPE_COVER_IMAGE = 2;
    const TYPE_POST_IMAGE = 3;
    const TYPE_MENTORSHIP_IMAGE = 4;

    protected $table = 'images';

    protected $fillable = [
        'user_id', 'type', 'image_type', 'post_img_id', 'file_name', 'cdate', 'object_id'
    ];

    public $timestamps = false;
}

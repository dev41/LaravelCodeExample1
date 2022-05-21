<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorContent extends Model
{
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;

    const TYPE_BECOME_MENTOR = 1;
    const TYPE_JOIN_TEAM = 2;

    protected $table = 'mentor_contents';

    protected $fillable = [
        'heading', 'back_image', 'below_heading', 'below_content', 'type', 'is_active'
    ];

    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorshipContent extends Model
{
    protected $table = 'mentorship_contents';

    protected $fillable = [
        'heading', 'back_image', 'below_heading', 'below_content', 'button_text', 'button_link', 'is_active'
    ];

    public $timestamps = false;
}

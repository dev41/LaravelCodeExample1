<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partnership extends Model
{
    protected $table = 'partnerships';

    protected $fillable = [
        'heading', 'content', 'belowcontent_button', 'button_link', 'back_image', 'middle_content', 'below_content',
        'is_active'
    ];

    public $timestamps = false;
}

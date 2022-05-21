<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareStory extends Model
{
    protected $table = 'sharestories';

    protected $fillable = [
        'heading', 'header_image', 'below_header_heading', 'below_header_content', 'codition_submit', 'submit_button',
        'is_active'
    ];

    public $timestamps = false;
}

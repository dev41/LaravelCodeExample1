<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSettings extends Model
{
    protected $table = 'smtp_details';

    protected $fillable = [
        'smtp_email', 'mail_server', 'mail_port', 'smtp_password'
    ];

    public $timestamps = false;
}

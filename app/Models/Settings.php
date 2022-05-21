<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'admin_email', 'phone', 'mobile', 'address', 'website', 'site_logo', 'favicon', 'facebook_url', 'twitter_url',
        'googleplus_url', 'youtube_url', 'linkedIn_url', 'footer_title', 'footer_content', 'booking_time_interval',
        'site_url', 'forgot_password_url', 'account_acctivation_link', 'aboutus_banner_image', 'contact_email',
        'support_email'
    ];

    public $timestamps = false;
}

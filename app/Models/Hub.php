<?php

namespace App\Models;

use App\Helpers\AzureBlob;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    const MEMBER_TYPE_NOT_MEMBER = 0;
    const MEMBER_TYPE_PENDING = 1;
    const MEMBER_TYPE_MEMBER = 2;
    const MEMBER_TYPE_NOT_ACCEPTED_MEMBER = 3;

    const TYPE_ONE_TIME = 0;
    const TYPE_RECURRING = 1;

    const PRIVACY_PRIVATE = 0;
    const PRIVACY_PUBLIC = 1;

    const NOT_ALL_DAY = 0;
    const ALL_DAY = 1;

    protected $table = 'hubs';

    protected $fillable = [
        'user_id', 'category_id', 'title', 'permalink', 'description', 'address', 'location', 'postal_code', 'lat',
        'lng', 'organizer', 'email', 'website', 'phone', 'start_date', 'end_date', 'type', 'privacy', 'image',
        'is_active', 'is_all_day'
    ];

    public function category()
    {
        return $this->hasOne('App\Models\HubCategory', 'id', 'category_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function invites()
    {
        return $this->hasMany('App\Models\HubInvite', 'hub_id', 'id');
    }

    public function recurringDays()
    {
        return $this->hasMany('App\Models\HubRecurringDay', 'hub_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'group_id', 'id')
            ->where('group_type', Post::GROUP_TYPE_HUB_POST);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', self::IS_ACTIVE);
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strip_tags($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = strip_tags($value, '<br>');
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = strip_tags($value);
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = strip_tags($value);
    }

    public function setPostalCodeAttribute($value)
    {
        $this->attributes['postal_code'] = strip_tags($value);
    }

    public function setOrganizerAttribute($value)
    {
        $this->attributes['organizer'] = strip_tags($value);
    }

    public function setWebsiteAttribute($value)
    {
        $this->attributes['website'] = strip_tags($value);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = strip_tags($value);
    }

    public function getImages()
    {
        if ($this->image) {
            return [
                'small' => AzureBlob::url(config('constants.files.hubs_path') . "{$this->id}/small/{$this->image}"),
                'medium' => AzureBlob::url(config('constants.files.hubs_path') . "{$this->id}/medium/{$this->image}"),
                'original' => AzureBlob::url(config('constants.files.hubs_path') . "{$this->id}/original/{$this->image}"),
            ];
        }

        return [
            'small' => AzureBlob::url(config('constants.files.hubs_path') . 'default/small/' . config('constants.images.default_hub_image_name')),
            'medium' => AzureBlob::url(config('constants.files.hubs_path') . 'default/medium/' . config('constants.images.default_hub_image_name')),
            'original' => AzureBlob::url(config('constants.files.hubs_path') . 'default/original/' . config('constants.images.default_hub_image_name')),
        ];
    }
}

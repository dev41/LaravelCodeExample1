<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPrivacySettings extends Model
{
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;

    // name_visible
    const FULL_NAME_VISIBLE = 1;
    const FIRST_NAME_ONLY_VISIBLE = 2;

    // name_visible_in_search_engine
    const NAME_NOT_VISIBLE_IN_SEARCH_ENGINE = 0;
    const NAME_VISIBLE_IN_SEARCH_ENGINE = 1;

    // found_email_address
    const NOT_FIND_BY_EMAIL = 0;
    const FIND_BY_EMAIL = 1;

    // found_phone_number
    const NOT_FIND_BY_PHONE = 0;
    const FIND_BY_PHONE = 1;

    // profile_picture_picture
    const PROFILE_PICTURE_VISIBLE_FOR_ALL = 0;
    const PROFILE_PICTURE_VISIBLE_FOR_CONNECTIONS = 1;

    // dob_visible
    const DOB_VISIBLE_FOR_ALL = 0;
    const DOB_VISIBLE_FOR_YOU = 1;
    const DOB_VISIBLE_FOR_CONNECTIONS = 2;

    // email_visible
    const EMAIL_VISIBLE_FOR_ALL = 0;
    const EMAIL_VISIBLE_FOR_YOU = 1;
    const EMAIL_VISIBLE_FOR_CONNECTIONS = 2;

    // phone_visible
    const PHONE_VISIBLE_FOR_ALL = 0;
    const PHONE_VISIBLE_FOR_YOU = 1;
    const PHONE_VISIBLE_FOR_CONNECTIONS = 2;

    // allow_connetion
    const NOT_ALLOW_CONNECTIONS_VIEW_ACTIVITY = 0;
    const ALLOW_CONNECTIONS_VIEW_ACTIVITY = 1;

    // all_post
    const DEFAULT_POST_VISIBILITY_FOR_ALL = 1;
    const DEFAULT_POST_VISIBILITY_FOR_CONNECTIONS = 2;

    // group_visible
    const GROUPS_VISIBLE_FOR_ALL = 0;
    const GROUPS_VISIBLE_FOR_CONNECTION = 1;

    // hub_visible
    const HUBS_VISIBLE_FOR_ALL = 0;
    const HUBS_VISIBLE_FOR_CONNECTIONS = 1;

    // photo_visible
    const PHOTOS_VISIBLE_FOR_ALL = 0;
    const PHOTOS_VISIBLE_FOR_CONNECTIONS = 1;

    // see_connection_list
    const CONNECTIONS_LIST_VISIBLE_FOR_ALL = 0;
    const CONNECTIONS_LIST_VISIBLE_FOR_CONNECTIONS = 1;
    const CONNECTIONS_LIST_VISIBLE_FOR_YOU = 2;

    // name_visible_book_review
    const IN_BOOR_REVIEW_NAME_VISIBLE_AS_FEMNESTY_MEMBER = 0;
    const IN_BOOR_REVIEW_NAME_VISIBLE_AS_FIRST_NAME = 1;

    protected $table = 'user_privacy_settings';

    protected $fillable = [
        'user_id', 'name_visible', 'name_visible_in_search_engine', 'found_email_address', 'found_phone_number',
        'profile_picture_picture', 'dob_visible', 'email_visible', 'phone_visible', 'allow_connetion', 'all_post',
        'group_visible', 'hub_visible', 'photo_visible', 'see_connection_list', 'name_visible_book_review'
    ];

    public $timestamps = false;
}

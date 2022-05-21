<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const IS_NOT_READ = 0;
    const IS_READ = 1;

    const NOT_SYSTEM = 0;
    const SYSTEM = 1;

    protected $table = 'messages';

    protected $fillable = [
        'chat_id', 'user_id', 'text', 'is_read', 'system', 'deleted_by'
    ];

    public function setTextAttribute($value)
    {
        $value = str_replace('&nbsp;', ' ', $value);
        $value = html_entity_decode($value);
        $value = strip_tags($value, '<br>');
        $value = trim($value);
        $value = str_ireplace('<br>', PHP_EOL, $value);
        $value = preg_replace('/^[\n|\n\s]+|[\n|\n\s]+$/', '', $value);

        $this->attributes['text'] = $value;
    }

    public function author()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function chat()
    {
        return $this->belongsTo('App\Models\Chat', 'chat_id', 'id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\File', 'object_id', 'id')
            ->where('type', File::TYPE_MESSAGES);
    }

    public function links()
    {
        return $this->hasOne('App\Models\MessageLink', 'message_id', 'id');
    }
}

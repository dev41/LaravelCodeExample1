<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubRecurringDay extends Model
{
    protected $table = 'hub_recurring_days';

    protected $fillable = [
        'hub_id', 'day_number'
    ];

    public function hub()
    {
        return $this->belongsTo('App\Models\Hub', 'hub_id', 'id');
    }
}

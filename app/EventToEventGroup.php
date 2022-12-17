<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventToEventGroup extends Model {

    protected $primaryKey = 'id';
    protected $table = 'event_to_event_group';
    public $timestamps = false;

}

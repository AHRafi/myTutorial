<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventGroupToCourse extends Model {

    protected $primaryKey = 'id';
    protected $table = 'event_group_to_course';
    public $timestamps = false;

}

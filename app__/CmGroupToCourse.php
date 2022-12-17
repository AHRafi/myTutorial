<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CmGroupToCourse extends Model {

    protected $primaryKey = 'id';
    protected $table = 'cm_group_to_course';
    public $timestamps = false;

}

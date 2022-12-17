<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class DsRemarks extends Model {

    protected $primaryKey = 'id';
    protected $table = 'ds_remarks';
    public $timestamps = false;


}

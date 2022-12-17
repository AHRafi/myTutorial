<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Configurable extends Model {

    protected $primaryKey = 'id';
    protected $table = 'configurable';
    public $timestamps = false;

    

}

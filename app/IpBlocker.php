<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class IpBlocker extends Model {

    protected $primaryKey = 'id';
    protected $table = 'ip_blocker';
    public $timestamps = false;

    

}

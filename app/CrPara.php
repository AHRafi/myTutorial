<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class CrPara extends Model {

    protected $primaryKey = 'id';
    protected $table = 'cr_para';
    public $timestamps = false;

}

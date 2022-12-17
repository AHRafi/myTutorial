<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model {

    protected $primaryKey = 'id';
    protected $table = 'district';
    public $timestamps = false;
}

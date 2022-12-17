<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    protected $primaryKey = 'id';
    protected $table = 'country';
    public $timestamps = false;

}

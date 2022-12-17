<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model {

    protected $primaryKey = 'id';
    protected $table = 'religion';
    public $timestamps = false;

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class CrTraitType extends Model {

    protected $primaryKey = 'id';
    protected $table = 'cr_trait_type';
    public $timestamps = false;

}

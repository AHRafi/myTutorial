<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class CrSentence extends Model {

    protected $primaryKey = 'id';
    protected $table = 'cr_sentence';
    public $timestamps = false;

}

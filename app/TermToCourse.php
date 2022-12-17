<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class TermToCourse extends Model {

    protected $primaryKey = 'id';
    protected $table = 'term_to_course';
    public $timestamps = false;

}

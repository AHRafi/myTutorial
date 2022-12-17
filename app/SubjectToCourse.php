<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubjectToCourse extends Model {

    protected $primaryKey = 'id';
    protected $table = 'subject_to_course';
    public $timestamps = false;

}

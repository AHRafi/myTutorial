<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class MutualAssessmentMarkingLock extends Model {

    protected $primaryKey = 'id';
    protected $table = 'mutual_assessment_marking_lock';
    public $timestamps = false;

}

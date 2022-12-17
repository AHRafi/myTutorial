<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class AssessmentActDeact extends Model {

    protected $primaryKey = 'id';
    protected $table = 'assessment_act_deact';
    public $timestamps = false;

}

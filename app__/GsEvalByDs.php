<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class GsEvalByDs extends Model
{
	protected $primaryKey = 'id';
	protected $table = 'gs_eval_by_ds';
        public $timestamps = false;

}

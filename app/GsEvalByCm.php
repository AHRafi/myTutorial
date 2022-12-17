<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class GsEvalByCm extends Model
{
	protected $primaryKey = 'id';
	protected $table = 'gs_eval_by_cm';
        public $timestamps = false;

}

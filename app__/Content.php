<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Content extends Model
{
	protected $primaryKey = 'id';
	protected $table = 'content';
        public $timestamps = false;

}

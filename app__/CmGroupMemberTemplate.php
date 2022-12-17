<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class CmGroupMemberTemplate extends Model {

    protected $primaryKey = 'id';
    protected $table = 'cm_group_member_template';
    public $timestamps = false;

}

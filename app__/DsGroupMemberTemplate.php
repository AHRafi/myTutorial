<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class DsGroupMemberTemplate extends Model {

    protected $primaryKey = 'id';
    protected $table = 'ds_group_member_template';
    public $timestamps = false;

}

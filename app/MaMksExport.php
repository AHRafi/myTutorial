<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class MaMksExport extends Model {

    protected $primaryKey = 'id';
    protected $table = 'ma_mks_export';
    public $timestamps = false;

    public static function boot() {
        parent::boot();

    }

}
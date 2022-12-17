<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class DeligateCiAcctToDs extends Model {

    protected $primaryKey = 'id';
    protected $table = 'deligate_ci_acct_to_ds';
    public $timestamps = false;

//    public static function boot() {
//        parent::boot();
//        static::updating(function($post) {
//            $post->updated_by = Auth::user()->id;
//        });
//    }

}

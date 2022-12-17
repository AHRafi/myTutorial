<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class CmMarkingGroup extends Model {

    protected $primaryKey = 'id';
    protected $table = 'cm_marking_group';
    public $timestamps = false;

//    public static function boot() {
//        parent::boot();
//        static::updating(function($post) {
//            $post->updated_by = Auth::user()->id;
//        });
//    }

}

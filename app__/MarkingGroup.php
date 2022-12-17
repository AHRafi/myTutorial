<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class MarkingGroup extends Model {

    protected $primaryKey = 'id';
    protected $table = 'marking_group';
    public $timestamps = false;

//    public static function boot() {
//        parent::boot();
//        static::updating(function($post) {
//            $post->updated_by = Auth::user()->id;
//        });
//    }

}

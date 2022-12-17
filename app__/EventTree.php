<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class EventTree extends Model {

    protected $primaryKey = 'id';
    protected $table = 'event_tree';
    public $timestamps = false;

//    public static function boot() {
//        parent::boot();
//        static::updating(function($post) {
//            $post->updated_by = Auth::user()->id;
//        });
//    }

}

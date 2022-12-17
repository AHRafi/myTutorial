<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class SubSubSubEventMksWt extends Model {

    protected $primaryKey = 'id';
    protected $table = 'sub_sub_sub_event_mks_wt';
    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

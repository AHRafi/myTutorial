<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class DsObsnMarkingLock extends Model {

    protected $primaryKey = 'id';
    protected $table = 'ds_obsn_marking_lock';
    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

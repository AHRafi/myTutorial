<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class DsObsnMarkingLimit extends Model {

    protected $primaryKey = 'id';
    protected $table = 'ds_obsn_marking_limit';
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

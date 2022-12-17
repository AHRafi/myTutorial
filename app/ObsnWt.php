<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ObsnWt extends Model {

    protected $primaryKey = 'id';
    protected $table = 'obsn_wt';
    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class WarningWt extends Model {

    protected $primaryKey = 'id';
    protected $table = 'warning_wt';
    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

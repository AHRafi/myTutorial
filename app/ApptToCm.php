<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ApptToCm extends Model {

    protected $primaryKey = 'id';
    protected $table = 'appt_to_cm';
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

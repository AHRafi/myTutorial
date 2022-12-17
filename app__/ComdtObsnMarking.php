<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ComdtObsnMarking extends Model {

    protected $primaryKey = 'id';
    protected $table = 'comdt_obsn_marking';
    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class TermToEvent extends Model {

    protected $primaryKey = 'id';
    protected $table = 'term_to_event';
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

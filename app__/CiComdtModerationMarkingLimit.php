<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class CiComdtModerationMarkingLimit extends Model {

    protected $primaryKey = 'id';
    protected $table = 'ci_comdt_moderation_marking_limit';
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

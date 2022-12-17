<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class UserPassport extends Model {

    protected $primaryKey = 'id';
    protected $table = 'user_passport_details';
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->updated_by = Auth::user()->id;
        });

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

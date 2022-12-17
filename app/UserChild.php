<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class UserChild extends Model {

    protected $primaryKey = 'id';
    protected $table = 'user_child';
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->updated_at = date('Y-m-d H:i:s');
        });

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class GsGrading extends Model {

    protected $primaryKey = 'id';
    protected $table = 'gs_grading';
    public $timestamps = true;

    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->created_by = Auth::user()->id;
            $post->updated_by = Auth::user()->id;
        });

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}
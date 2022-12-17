<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class EventAssessmentMarkingLock extends Model {

    protected $primaryKey = 'id';
    protected $table = 'event_assessment_marking_lock';
    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }

}

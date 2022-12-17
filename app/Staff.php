<?php

namespace App;
use Auth;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'staff';
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
    
    public function rank() {
        return $this->belongsTo('App\Rank', 'rank_id');
    }
    public function appointment() {
        return $this->belongsTo('App\Appointment', 'appointment_id');
    }
    
}

<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class User extends Authenticatable {

    use Notifiable;

    protected $hidden = [
        'password', 'remember_token', 'conf_password',
    ];
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

    public function userGroup() {
        return $this->belongsTo('App\UserGroup', 'group_id');
    }

    public function rank() {
        return $this->belongsTo('App\Rank', 'rank_id');
    }
    public function appointment() {
        return $this->belongsTo('App\Appointment', 'appointment_id');
    }
    public function studentAppointment() {
        return $this->belongsTo('App\StudentAppointment', 'appointment_id');
    }

}

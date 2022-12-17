<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class CmToSyn extends Model {

    protected $primaryKey = 'id';
    protected $table = 'cm_to_syn';
    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::updating(function($post) {
            $post->updated_by = Auth::user()->id;
        });
    }
    
//    public function SpecialSkill(){
//        return $this->hasMany('App\SpecialSkill', 'recruit_id', 'id');
//    }

}

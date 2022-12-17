<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SubjectToDs extends Model
{
    protected $primaryKey = 'id';
	protected $table = 'subject_to_ds';
    public $timestamps = true;

	public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->updated_at = Carbon::now();
            $post->updated_by = Auth::user()->id ?? 1;
        });

        static::updating(function($post) {
            $post->updated_at = Carbon::now();
            $post->updated_by = Auth::user()->id;
        });
    }
}

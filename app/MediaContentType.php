<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class MediaContentType extends Model {

    protected $primaryKey = 'id';
    protected $table = 'media_content_type';
    public $timestamps = false;

}

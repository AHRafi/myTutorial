<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentClassification extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'content_classification';
    public $timestamps = false;
}

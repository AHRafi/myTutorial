<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Board extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'board';
    public $timestamps = false;
}
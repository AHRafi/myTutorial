<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeligateReportsToDs extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'deligate_reports_to_ds';
    public $timestamps = false;
}

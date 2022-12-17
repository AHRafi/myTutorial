<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrSentenceToTrait extends Model {

    protected $primaryKey = 'id';
    protected $table = 'cr_sentence_to_trait';
    public $timestamps = false;

}

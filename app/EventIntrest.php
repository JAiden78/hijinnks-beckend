<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventIntrest extends Model
{
    function interest(){
        return $this->hasOne(Interest::class,'id','interest_id');
    }
}

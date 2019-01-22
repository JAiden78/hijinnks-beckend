<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    function Intrest(){
         return $this->hasOne(Interest::class, 'id','interest_id');
    }
}

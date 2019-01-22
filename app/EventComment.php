<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventComment extends Model
{
    function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}

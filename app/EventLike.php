<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventLike extends Model
{
    function user(){
        return $this->hasOne(User::class,'id','user_id')->select('id','username','photo');
    }
}

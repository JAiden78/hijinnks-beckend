<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    function sender(){
        return $this->hasOne(User::class,'id','user_id')->select('id','username','photo');
    }
}

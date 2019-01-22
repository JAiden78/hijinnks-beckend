<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventFollower extends Model
{
    function Invites(){
        return $this->hasOne(Event::class, 'id','event_id');
    }
    function user(){
        return $this->hasOne(User::class, 'id','user_id')->select('id','username','photo');
    }
}

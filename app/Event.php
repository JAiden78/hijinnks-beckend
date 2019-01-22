<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    function user(){
        return $this->hasOne(User::class,'id','user_id')->select('id','username','photo');
    }
    function comments(){
        return $this->hasMany(EventComment::class,'event_id');
    }
    function likes(){
     return $this->hasMany(EventLike::class,'event_id');   
    }
    function interests(){
        return $this->hasMany(EventIntrest::class,'event_id');
    }
    function arrived(){
       return $this->hasMany(EventFollower::class,'event_id')->where('is_rsvpd',1);    
    }
    function attachments(){
       return $this->hasMany(EventAttachment::class,'event_id');    
    }
    function Invites(){
       return $this->hasMany(EventFollower::class,'event_id');    
    }
    
    function userInvites(){
       return $this->hasMany(EventFollower::class,'event_id')->where('user_id',Auth::user()->id);    
    }
    
    function userArrived(){
       return $this->hasMany(EventFollower::class,'event_id')->where('user_id',Auth::user()->id)->where('is_rsvpd',1);    
    }
    function userLiked(){
      return $this->hasMany(EventLike::class,'event_id')->where('user_id',Auth::user()->id);   
    }
    
    function Reoccurance(){
       return $this->hasOne(EventReoccurance::class,'event_id');    
    }
    
    public function getCoverAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }
//    event_end_date
    public function getEventEndDateAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }
    public function getUtcEventEndDateAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }
}

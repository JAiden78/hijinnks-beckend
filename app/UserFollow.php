<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserFollow extends Model {

    function follower() {
        return $this->hasOne(User::class,'id', 'user_id');
    }

    function following() {
        return $this->hasOne(User::class,'id','followed_id');
    }

    function isFollowing() {
        return $this->hasOne(UserFollow::class, 'followed_id', 'user_id')->where('user_id',Auth::user()->id);
    }
    function invited() {
        return $this->hasMany(EventFollower::class, 'user_id','user_id');
    }
    
    function invites() {
        return $this->hasMany(EventFollower::class, 'user_id','followed_id');
    }
}

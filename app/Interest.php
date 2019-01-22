<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Interest extends Model
{
    function user(){
         return $this->hasMany(UserInterest::class, 'interest_id');
    }
    function event(){
         return $this->hasMany(EventIntrest::class, 'interest_id');
    }
    function isAdded(){
         return $this->hasMany(UserInterest::class, 'interest_id')->where('user_id',Auth::user()->id);
    }
}

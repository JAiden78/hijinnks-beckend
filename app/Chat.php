<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Chat extends Model
{
     function sender() {
        return $this->hasOne('\App\User', 'id', 'sender_id')->select(['id', 'username', 'photo', 'cover','gender']);
    }
    
    function receiver() {
        return $this->hasOne('\App\User', 'id', 'receiver_id')->select(['id', 'username', 'photo', 'cover','gender']);
    }
    
    function lastMessage() {
        return $this->hasOne('\App\ChatMessage', 'id', 'last_message_id');
    }
    
    function messages(){
        return $this->hasMany('\App\ChatMessage', 'chat_id')->where('receiver_id',Auth::user()->id);
    }
    function allMessages(){
        return $this->hasMany('\App\ChatMessage', 'chat_id');
    }
}

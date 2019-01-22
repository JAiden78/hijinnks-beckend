<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    function sender() {
        return $this->hasOne('\App\User', 'id', 'sender_id')->select(['id', 'username', 'photo', 'cover','gender']);
    }
    
    function receiver() {
        return $this->hasOne('\App\User', 'id', 'receiver_id')->select(['id', 'username', 'photo', 'cover','gender']);
    }
    
    function chatUser() {
        return $this->hasOne('\App\User', 'id', 'chat_id');
    }
}

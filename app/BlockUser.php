<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    function user() {
        return $this->hasOne(User::class,'id', 'blocked_id');
    }
}

<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    function invited() {
        return $this->hasMany(EventFollower::class, 'user_id');
    }

    function isFollowed() {
        return $this->hasOne(UserFollow::class, 'user_id')->where('followed_id', Auth::user()->id);
    }

    function isFollowing() {
        return $this->hasOne(UserFollow::class, 'followed_id')->where('user_id', Auth::user()->id);
    }

    function Following() {
        return $this->hasMany(UserFollow::class, 'user_id')->where('is_blocked', 0);
    }

    function Followers() {
        return $this->hasMany(UserFollow::class, 'followed_id')->where('is_blocked', 0);
    }

    function Intrest() {
        return $this->hasMany(UserInterest::class, 'user_id');
    }

    function Invites() {
        return $this->hasMany(EventFollower::class, 'user_id');
    }

    function Rsvp() {
        return $this->hasMany(EventFollower::class, 'user_id')->where('is_rsvpd', 1);
    }

    function Like() {
        return $this->hasMany(EventLike::class, 'user_id');
    }

    function Comment() {
        return $this->hasMany(EventComment::class, 'user_id');
    }

    function Share() {
        return $this->hasMany(EventShare::class, 'user_id');
    }

    function Event() {
        return $this->hasMany(Event::class, 'user_id');
    }

    public function getAttributes() {
        $attributes = $this->toArray();
        foreach ($attributes as $attr) {
            if (is_null($attr)) {
                $attr = '';
            }
            if (!$attr) {
                $attr = '';
            }
            if ($attr == null) {
                $attr = '';
            }
        }
        return $attributes;
    }

    public function getPhotoAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getEmailAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getPasswordAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getDeviceIdAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getFbIdAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getTwitterIdAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getLatAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getLngAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getLocationAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getRememberTokenAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getTimeZoneAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getEmailCodeAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getSessionTokenAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

    public function getDescriptionAttribute($value) {
        if (is_null($value)) {
            $value = '';
        }
        return $value;
    }

}

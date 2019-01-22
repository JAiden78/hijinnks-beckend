<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
//Models
use App\User;
use App\UserInterest;
use App\Interest;
use App\EventFollower;
use App\Event;
use App\BlockUser;
use App\UserFollow;
use App\Notification;


class UserController extends Controller {

    private $userId;
    private $lat;
    private $lng;
    private $user;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            $this->user = Auth::user();
            $this->lat = Auth::user()->lat;
            $this->lng = Auth::user()->lng;
            return $next($request);
        });
    }

    function getUser($userid) {
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $events = EventFollower::select('event_id')->where('user_id', $userid)->get()->toArray();
        $data['user'] = User::where('id', $userid)
                ->withCount('isFollowing', 'Rsvp', 'Following', 'Followers', 'Intrest', 'Invites')
                ->first();
        $data['events'] = Event::selectRaw("*,( 6371 * acos( cos( radians($this->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($this->lng) ) + sin( radians($this->lat) ) * sin( radians(lat) ) ) ) AS distance")
                ->whereNotIn('user_id', $blocked_ids)
                ->whereIn('id', $events)
                ->with('user', 'likes.user', 'comments.user', 'attachments', 'Invites.user', 'interests', 'interests.interest', 'arrived.user')
                ->with(['comments' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['likes' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['Invites' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['arrived' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->orderBy('event_date', 'asc')->orderBy('distance', 'asc')
                ->withCount('likes', 'arrived', 'userArrived', 'userLiked')
                ->get();

        $data['my_events'] = Event::selectRaw("*,( 6371 * acos( cos( radians($this->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($this->lng) ) + sin( radians($this->lat) ) * sin( radians(lat) ) ) ) AS distance")
                ->whereNotIn('user_id', $blocked_ids)
                ->where('user_id', $userid)
                ->with('user', 'likes.user', 'comments.user', 'attachments', 'Invites.user', 'interests', 'interests.interest', 'arrived.user')
                ->with(['comments' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['likes' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['Invites' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['arrived' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->orderBy('event_date', 'asc')->orderBy('distance', 'asc')
                ->withCount('likes', 'arrived', 'userArrived', 'userLiked')
                ->get();
        $data['is_blocked'] = BlockUser::where(array('user_id' => $userid, 'blocked_id' => $this->userId, 'other_blocked' => 0))->count();
        $data['other_blocked'] = BlockUser::where(array('user_id' => $userid, 'blocked_id' => $this->userId, 'other_blocked' => 1))->count();
        return sendSuccess('', $data);
    }

    function getAllIntrests() {
        $intrests = Interest::orderBy('title', 'desc')->get();
        return sendSuccess('', $intrests);
    }

    function getAllUserIntrests() {
        $intrests = Interest::withCount('isAdded')->orderBy('title', 'desc')->get();
        return sendSuccess('', $intrests);
    }

    function getIntrest($userid) {
        $intrests = UserInterest::where('user_id', $userid)->with('Intrest')->get();
        return sendSuccess('', $intrests);
    }

    function changeCover(Request $request) {
        $validation = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        if (!$validation) {
            return sendError($validation, 403);
        }
        $user = User::find($this->userId);
        $filename = addFile($request['image'], 'cover');
        $user->cover = asset('public/images/cover/' . $filename);
        $user->save();
        return sendSuccess("Cover Updated", $user->cover);
    }

    function changeProfileImage(Request $request) {
        $validation = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        if (!$validation) {
            return sendError($validation, 403);
        }
        $user = User::find($this->userId);
        $filename = addFile($request['image'], 'profiepic');
        $user->photo = asset('public/images/profiepic/' . $filename);
        $user->save();
        return sendSuccess("Profile Image Updated", $user->photo);
    }

    function changeProfile(Request $request) {
        $validation = $this->validate($request, [
            'lat' => 'required',
            'lng' => 'required',
            'name' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 403);
        }
        $user = User::find($this->userId);
        $user->location = $request['location'];
        $user->lat = $request['lat'];
        $user->lng = $request['lng'];
        $user->description = $request['description'];
        $user->username = $request['name'];
        $user->save();
        return sendSuccess("Profile Updated", $user);
    }

    function changePassword(Request $request) {
        $validation = $this->validate($request, [
            'password' => 'required',
            'old_password' => 'required',
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }
        $password = Auth::user()->password;
        if (Hash::check($request['old_password'], $password)) {
            $user = User::find($this->userId);
            $user->password = bcrypt($request['password']);
            $user->save();
            return sendSuccess("Password Updated successfully", '');
        } else {
            return sendError("Invalid Old Password", 410);
        }
    }

    function addIntrest(Request $request) {
        $validation = $this->validate($request, [
            'intrest_ids' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 403);
        }


        $intrests = explode(',', $request['intrest_ids']);
        foreach ($intrests as $intrest) {
            $check_intrest = UserInterest::where(array('user_id' => $this->userId, 'interest_id' => $intrest))->first();
            if (!$check_intrest) {
                $add_intrest = new UserInterest;
                $add_intrest->user_id = $this->userId;
                $add_intrest->interest_id = $intrest;
                $add_intrest->save();
            }
        }
        return sendSuccess("Intrests added successfully", '');
    }

    function deleteIntrest($id) {
        UserInterest::where('interest_id', $id)->where('user_id', $this->userId)->delete();
        return sendSuccess("Intrests deleted successfully", '');
    }

    function updatePrivate(Request $request) {
        $validation = $this->validate($request, [
            'is_private' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }
        $user = User::find($this->userId);
        $user->is_private = ($request['is_private']);
        $user->save();
        return sendSuccess("Profile Updated successfully", '');
    }

    function updateNotification(Request $request) {
        $validation = $this->validate($request, [
            'push_notification' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }
        $user = User::find($this->userId);
        $user->push_notification = $request['push_notification'];
        $user->save();
        return sendSuccess("Profile Updated successfully", '');
    }

    function blockUnBlockUser(Request $request) {
        $validation = $this->validate($request, [
            'user_id' => 'required',
            'is_block' => 'required',
        ]);
        if (!$validation) {
            return sendError($validation, 403);
        }
        if ($request['is_block']) {
            $check_block = BlockUser::where(array('user_id' => $this->userId, 'blocked_id' => $request['user_id']))->first();
            if (!$check_block) {
                $add_block = new BlockUser;
                $add_block->user_id = $this->userId;
                $add_block->blocked_id = $request['user_id'];
                $add_block->save();
                $add_other_block = new BlockUser;
                $add_other_block->user_id = $request['user_id'];
                $add_other_block->blocked_id = $this->userId;
                $add_other_block->other_blocked = 1;
                $add_other_block->save();
                UserFollow::where(array('user_id' => $this->userId, 'followed_id' => $request['user_id']))->update(['is_blocked' => 1]);
                UserFollow::where(array('user_id' => $request['user_id'], 'followed_id' => $this->userId))->update(['is_blocked' => 1]);
                return sendSuccess("User Blocked successfully", '');
            } else {
                return sendError('Already Blocked', 403);
            }
        } else {
            BlockUser::where(array('user_id' => $this->userId, 'blocked_id' => $request['user_id']))->delete();
            BlockUser::where(array('user_id' => $request['user_id'], 'blocked_id' => $this->userId))->delete();
            UserFollow::where(array('user_id' => $this->userId, 'followed_id' => $request['user_id']))->update(['is_blocked' => 0]);
            UserFollow::where(array('user_id' => $request['user_id'], 'followed_id' => $this->userId))->update(['is_blocked' => 0]);
            return sendSuccess("User UnBlocked successfully", '');
        }
    }

    function getBlockUser() {
        $getblocked_user = BlockUser::where(array('user_id' => $this->userId, 'other_blocked' => 0))
                        ->orderBy('created_at', 'desc')->with('user')->get();
        return sendSuccess("", $getblocked_user);
    }

    function getUserBlocked() {
        $getblocked_user = BlockUser::where(array('blocked_id' => $this->userId, 'other_blocked' => 0))
                        ->orderBy('created_at', 'desc')->with('user')->get();
        return sendSuccess("", $getblocked_user);
    }

    function getBlocked() {
        $data['blocked_users'] = BlockUser::where(array('user_id' => $this->userId, 'other_blocked' => 0))
                        ->orderBy('created_at', 'desc')->with('user')->get();
        $data['user_blocks'] = BlockUser::where(array('blocked_id' => $this->userId, 'other_blocked' => 0))
                        ->orderBy('created_at', 'desc')->with('user')->get();
        return sendSuccess("", $data);
    }

    function sendMail(Request $request) {
        $validation = $this->validate($request, [
            'message' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }
        $data['user'] = $this->user->username;
        if ($this->user->email) {
            $email = $this->user->email;
        } else {
            $email = 'Support@hijinnksapp.com';
        }
        $data['probleam'] = $request['message'];
        Mail::send('emails.support', $data, function ($m) use($email) {
            $m->from($email, 'Hijinnks App');
            $m->to('info@hijinnks.com', 'Hijinnks')->subject('Problem Reported');
        });
        return sendSuccess("Email Sent Successfully", '');
    }

    function getNotifications() {
        $notifications = Notification::where('on_user', $this->userId)->orderBy('id', 'desc')
                        ->with('sender')->get();
        Notification::where('on_user', $this->userId)->update(['is_read' => 1]);
        return sendSuccess("", $notifications);
    }

    function addGender(Request $request) {
        $validation = $this->validate($request, [
            'gender' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }
        $user = $this->user;
        $user->gender = $request->gender;
        $user->save();
        return sendSuccess("Gender added successfully", $user);
    }

}

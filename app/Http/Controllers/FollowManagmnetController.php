<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserFollow;
use Illuminate\Support\Facades\Auth;
use App\BlockUser;
use App\Notification;

class FollowManagmnetController extends Controller {

    private $userId;
    private $userName;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            $this->userName = Auth::user()->username;
            return $next($request);
        });
    }

    function addFollower(Request $request) {
        $validation = $this->validate($request, [
            'followed_id' => 'required',
        ]);
        if (!$validation) {
            return sendError($validation, 413);
        }
        $follower_id = $this->userId;
        $check_follow = UserFollow::where(array('user_id' => $follower_id, 'followed_id' => $request['followed_id']))->first();
        if ($check_follow) {
            return sendError("Already Following", 200);
//            return Response::json(array('status' => 'error', 'errorMessage' => 'Already Following'));
        }
        $add_follow = new UserFollow;
        $add_follow->user_id = $follower_id;
        $add_follow->followed_id = $request['followed_id'];
        $add_follow->save();
        $messagex = $this->userName . ' is following you.';
        $data['message'] = $add_follow;

        //Save notification data
        $notification = new Notification;
        $notification->user_id = $this->userId;
        $notification->on_user = $request['followed_id'];
        $notification->notification_text = $messagex;
        $notification->activity_text = 'you follow a user.';
        $notification->type = 'Follow';
        $notification->save();
        //Send notification
        sendNotification($request['followed_id'], $data, '', $messagex);

        return sendSuccess("Following added successfully", '');
    }

    function unFollow(Request $request) {
        $validation = $this->validate($request, [
            'followed_id' => 'required',
        ]);
        if (!$validation) {
            return sendError($validation, 414);
        }
        $follower_id = $this->userId;
        $check_follow = UserFollow::where(array('user_id' => $follower_id, 'followed_id' => $request['followed_id']))->first();
        if (!$check_follow) {
            return sendError("Not Following", 414);
        }
        $check_follow->delete();
        $messagex = $this->userName . ' unfollows you.';
        $data['message'] = $request['followed_id'];
//        sendNotification($request['followed_id'],$data,'', $messagex);
        return sendSuccess("Unfollow successfully", "");
    }

    function getFollowing($user_id) {
        $skip = $_GET['skip'] * 10;
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $data['followings'] = UserFollow::where('user_id', $user_id)
                ->with('following')->where('is_blocked', 0)
                ->whereNotIn('followed_id', $blocked_ids)
                ->withCount('invites')
//                ->take(10)->skip($skip)
                ->get();
        $sorted = $data['followings']->sortBy('following.username');
        $data['followings'] = $sorted->values()->all();
        return sendSuccess('', $data);
    }

    function getFollowers($user_id) {
        $skip = $_GET['skip'] * 10;
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $data['followers'] = UserFollow::where('followed_id', $user_id)
                ->whereNotIn('user_id', $blocked_ids)
                ->with('follower')
                ->withCount('isFollowing', 'invited')
                ->where('is_blocked', 0)
//                        ->take(10)
//                        ->skip($skip)
                ->get();
        $sorted = $data['followers']->sortBy('follower.username');
        $data['followers'] = $sorted->values()->all();
        return sendSuccess('', $data);
    }

}

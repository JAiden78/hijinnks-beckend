<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventComment;
use App\EventLike;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Event;
use App\EventFollower;
use App\UserFollow;
use App\EventShare;
use App\BlockUser;
use Carbon\Carbon;
use App\Notification;

class ActionController extends Controller {

    private $userId;
    private $userName;
    private $lat;
    private $lng;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            $this->userName = Auth::user()->username;
            $this->lat = Auth::user()->lat;
            $this->lng = Auth::user()->lng;
            return $next($request);
        });
    }

    function addLike(Request $request) {

        $validation = $this->validate($request, [
            'event_id' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 410);
        }
        $check_like = EventLike::where(array('user_id' => $this->userId, 'event_id' => $request['event_id']))->first();
        if ($check_like) {
            return sendError("Like Already Added", 411);
        }
        $add_like = new EventLike;
        $add_like->user_id = $this->userId;
        $add_like->event_id = $request['event_id'];
        $add_like->save();
        $event = Event::find($request['event_id']);
        if ($this->userId != $event->user_id) {

            $messagex = $this->userName . ' liked your event ' . $event->title;
            //Save notification data
            $notification = new Notification;
            $notification->user_id = $this->userId;
            $notification->event_id = $request['event_id'];
            $notification->on_user = $event->user_id;
            $notification->notification_text = $messagex;
            $notification->activity_text = 'You liked an event.';
            $notification->type = 'Like';
            $notification->save();
            $data['event'] = $event;

            sendNotification($event->user_id, $data, '', $messagex);
        }
        return sendSuccess("Event Like SuccessFully Added", "");
    }

    function addShare(Request $request) {

        $validation = $this->validate($request, [
            'event_id' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 410);
        }
        $check_like = EventShare::where(array('user_id' => $this->userId, 'event_id' => $request['event_id']))->first();
        if ($check_like) {
            return sendError("Share Already Added", 411);
        }
        $add_like = new EventShare;
        $add_like->user_id = $this->userId;
        $add_like->event_id = $request['event_id'];
        $add_like->save();
        return sendSuccess("Event Share SuccessFully Added", "");
    }

    function addRsvp(Request $request) {
        $validation = $this->validate($request, [
            'event_id' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 410);
        }
                
        $event = Event::find($request['event_id']);
        $add_rsvp = EventFollower::where(array('user_id' => $this->userId, 'event_id' => $request['event_id']))->first();

        if (!$add_rsvp) {
            $add_rsvp = new EventFollower;
            $add_rsvp->user_id = $this->userId;
            $add_rsvp->event_id = $request['event_id'];
            $add_rsvp->only_rsvpd = 1;
            $add_rsvp->is_rsvpd = $request['is_rsvpd'];
        } else {
       
            if ($request['is_rsvpd'] != 1) {
                if ($add_rsvp->only_rsvpd == 1) {
                    $add_rsvp->delete();
                } else {
                    $add_rsvp->is_rsvpd = $request['is_rsvpd'];
                     $add_rsvp->save();
                }
            } else {
                $add_rsvp->is_rsvpd = $request['is_rsvpd'];
                $add_rsvp->save();
            }
           
        }

        if ($request['is_rsvpd'] == 1) {
            $add_rsvp->save();

            $messagex = $this->userName . ' joined the ' . $event->title;
            //Save notification data
            $notification = new Notification;
            $notification->user_id = $this->userId;
            $notification->event_id = $request['event_id'];
            $notification->on_user = $event->user_id;
            $notification->notification_text = $messagex;
            $notification->activity_text = 'You joined an event.';
            $notification->type = 'Join';
            $notification->save();
            $data['event'] = $event;
            //Send notification
//            $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $event->user_id))->get()->toArray();
//            $other_user=User::find($event->user_id);
//            $event = Event::selectRaw("*,( 6371 * acos( cos( radians($other_user->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($other_user->lng) ) + sin( radians($other_user->lat) ) * sin( radians(lat) ) ) ) AS distance")
//                       ->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'Invites.user', 'interests.interest', 'arrived.user')
//                        ->with(['comments' => function($query)use ($blocked_ids) {
//                                $query->whereNotIn('user_id', $blocked_ids);
//                            }])->with(['likes' => function($query)use ($blocked_ids) {
//                                $query->whereNotIn('user_id', $blocked_ids);
//                            }])->with(['Invites' => function($query)use ($blocked_ids) {
//                                $query->whereNotIn('user_id', $blocked_ids);
//                            }])->with(['arrived' => function($query)use ($blocked_ids) {
//                                $query->whereNotIn('user_id', $blocked_ids);
//                            }])
//                        ->orderBy('utc_event_time', 'desc')->orderBy('distance', 'asc')
//                        ->where('id', $request['event_id'])
//                        ->withCount('likes', 'arrived', 'userArrived', 'userLiked')->first();
            sendNotification($event->user_id, $data, '', $messagex);
        }



        return sendSuccess("Event Rsvpd SuccessFully Updated", $event);
    }

    function addUnLike(Request $request) {
        $user = Auth::user()->id;
        $validation = $this->validate($request, [
            'event_id' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 410);
        }
        EventLike::where(array('user_id' => $user, 'event_id' => $request['event_id']))->delete();
        return sendSuccess("Event UnLike SuccessFully Added", "");
    }

    function addComment(Request $request) {

        $validation = $this->validate($request, [
            'event_id' => 'required',
            'comment' => 'required',
        ]);
        if (!$validation) {
            return sendError($validation, 410);
        }
        $add_comment = new EventComment;
        $add_comment->user_id = $this->userId;
        $add_comment->event_id = $request['event_id'];
        $add_comment->comment = $request['comment'];
        $add_comment->save();
        $event = Event::find($request['event_id']);
        if ($this->userId != $event->user_id) {
            $messagex = $this->userName . ' commented on your event ' . $event->title;
            //Save notification data
            $notification = new Notification;
            $notification->user_id = $this->userId;
            $notification->event_id = $request['event_id'];
            $notification->on_user = $event->user_id;
            $notification->notification_text = $messagex;
            $notification->activity_text = 'You comment an event.';
            $notification->type = 'Comment';
            $notification->save();
            $data['event'] = $event;

            sendNotification($event->user_id, $data, '', $messagex);
        }
        return sendSuccess("Event Comment SuccessFully Added", "");
    }

    function searchUser() {
        $q = $_GET['q'];
        $skip = 0;
        if (isset($_GET['skip'])) {
            $skip = $_GET['skip'] * 10;
        }
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $follower = UserFollow::select('followed_id')->where(array('user_id' => $this->userId))->get()->toArray();
        $data['users'] = User::select('id', 'username', 'photo', 'user_type')
                        ->whereNotIn('id', $blocked_ids)->where('username', 'like', "%$q%")
                        ->where('user_type', '!=', 1)
                        ->where('id', '!=', $this->userId)
//                        ->where(function($q) use($follower) {
////                            $q->where('is_private', 0)->orWhereIn('id', $follower);
//                        })
                        ->orderBy('username', 'asc')
                        ->withCount('isFollowed', 'isFollowing', 'Invites')
                        ->with('Invites')
                        ->take(10)->skip($skip)->get();
        return sendSuccess('', $data);
    }

    function getAllUser() {
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $data['users'] = User::select('id', 'username', 'photo', 'user_type')
                ->whereNotIn('id', $blocked_ids)->where('id', '!=', $this->userId)
                ->where('user_type', 0)
                ->withCount('isFollowed', 'isFollowing', 'Invites')
                ->with('Invites')->orderBy('username', 'asc')->whereNotIn('id', $blocked_ids)
                ->get();
        return sendSuccess('', $data);
    }

    function search() {
        $q = $_GET['q'];
        $skip = 0;
        if (isset($_GET['skip'])) {
            $skip = $_GET['skip'] * 10;
        }
        $follower = UserFollow::select('followed_id')->where(array('user_id' => $this->userId))->get()->toArray();
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $data['users'] = User::select('id', 'username', 'photo', 'user_type')
                ->whereNotIn('id', $blocked_ids)->where('username', 'like', "%$q%")
                ->where('id', '!=', $this->userId)
                ->where('user_type', 0)
//                ->where(function($q) use($follower) {
//                    $q->where('is_private', 0)->orWhereIn('id', $follower);
//                })
                ->withCount('isFollowed', 'isFollowing', 'Invites')
                ->with('Invites')->orderBy('username', 'asc')
                ->take(10)->skip($skip)
                ->get();
        $data['events'] = Event::selectRaw("*,( 6371 * acos( cos( radians($this->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($this->lng) ) + sin( radians($this->lat) ) * sin( radians(lat) ) ) ) AS distance")
                        ->where('utc_event_end_date', '>', Carbon::now())->where(function($query) use($q) {
                            $query->where('title', 'like', "%$q%")->orwhere('description', 'like', "%$q%");
                        })->where('utc_event_end_date', '>=', Carbon::now())
                        ->with('user', 'likes', 'likes.user', 'comments', 'comments.user', 'attachments', 'interests', 'interests.interest', 'Invites.user')
                        ->whereHas('user', function ($query) use($follower) {
                            $query->where('is_private', 0)->orWhere('id', $this->userId)->orWhereIn('id', $follower);
                        })
                        ->whereNotIn('user_id', $blocked_ids)
                        ->orderBy('utc_event_time', 'asc')
                                ->orderBy('distance', 'asc')
                        ->withCount('likes', 'arrived', 'userArrived', 'userLiked')->take(10)->skip($skip)->get();
        return sendSuccess('', $data);
    }

}

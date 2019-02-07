<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ChatMessage;
use App\EventFollower;
use App\EventLike;
use App\UserFollow;
use App\UserInterest;
use App\Event;
use App\EventIntrest;
use Illuminate\Support\Facades\DB;
use App\BlockUser;
use Carbon\Carbon;
use App\Notification;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller {

    private $userId;
    private $lat;
    private $lng;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            $this->lat = Auth::user()->lat;
            $this->lng = Auth::user()->lng;
            return $next($request);
        });
    }

    function index() {
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $data['chat_count'] = ChatMessage::where(array('receiver_id' => $this->userId, 'is_read' => 0))->count();
        $data['rsvpd_count'] = EventFollower::where(array('user_id' => $this->userId, 'is_rsvpd' => 1))->count();
        $data['invite_count'] = EventFollower::where(array('user_id' => $this->userId))->count();
        $data['liked_events'] = EventLike::where(array('user_id' => $this->userId))->count();
        $data['followings_count'] = UserFollow::where(array('user_id' => $this->userId))->count();
        $data['follower_count'] = UserFollow::where(array('followed_id' => $this->userId))->count();
        $data['unread_notifications'] = Notification::where(['on_user' => $this->userId, 'is_read' => 0])->count();

        $follower = UserFollow::select('followed_id')->where(array('user_id' => $this->userId))->get()->toArray();
        $user_intrests = UserInterest::select('interest_id')->where('user_id', $this->userId)->get()->toArray();
        $data['events_count'] = Event::whereNotIn('user_id', $blocked_ids)->where('utc_event_end_date', '>', Carbon::now())->whereHas('user', function ($query)use($follower) {
                    $query->where('is_private', 0)->orWhere('id', $this->userId)->orWhereIn('id', $follower);
                })->count();
        $data['my_events'] = Event::where('user_id', $this->userId)->count();
        return sendSuccess('', $data);
    }

    function getRsvpEvent($user_id) {
        $skip = $_GET['skip'] * 10;
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $events = EventFollower::select('event_id')->where(array('user_id' => $user_id, 'is_rsvpd' => 1))->get()->toArray();
//return sendSuccess('', $events);
        $data['events'] = Event::selectRaw("*,3956 * 2 * ASIN(SQRT(POWER(SIN(($this->lat - lat) * pi()/180 / 2), 2) + COS($this->lat * pi()/180) * COS(lat * pi()/180) * POWER(SIN(($this->lng - lng) *  pi()/180 / 2), 2) )) as distance")
//                        ->whereNotIn('user_id', $blocked_ids)
                ->whereIn('id', $events)
                ->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'interests.interest', 'Invites.user', 'arrived.user')
                ->with(['comments' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])
                ->with(['likes' => function($query2)use ($blocked_ids) {
                        $query2->whereNotIn('user_id', $blocked_ids);
                    }])
                ->with(['Invites' => function($query3)use ($blocked_ids) {
                        $query3->whereNotIn('user_id', $blocked_ids);
                    }])
                ->with(['arrived' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])
//                            ->where('utc_event_end_date', '>', Carbon::now())
                ->orderBy('event_date', 'asc')->orderBy('distance', 'asc')
                ->withCount('likes', 'arrived', 'userArrived', 'userLiked')
                ->take(10)->skip($skip)
                ->get();
        return sendSuccess('', $data);
    }

    function getFavrities() {
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $skip = $_GET['skip'] * 10;
        $events = EventLike::select('event_id')->where(array('user_id' => $this->userId))->get()->toArray();
        $data['events'] = Event::selectRaw("*,3956 * 2 * ASIN(SQRT(POWER(SIN(($this->lat - lat) * pi()/180 / 2), 2) + COS($this->lat * pi()/180) * COS(lat * pi()/180) * POWER(SIN(($this->lng - lng) *  pi()/180 / 2), 2) )) as distance")
                        ->whereNotIn('user_id', $blocked_ids)->whereIn('id', $events)
                        ->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'interests.interest', 'Invites.user', 'arrived.user')
                        ->with(['comments' => function($query)use ($blocked_ids) {
                                $query->whereNotIn('user_id', $blocked_ids);
                            }])->with(['likes' => function($query)use ($blocked_ids) {
                                $query->whereNotIn('user_id', $blocked_ids);
                            }])->with(['Invites' => function($query)use ($blocked_ids) {
                                $query->whereNotIn('user_id', $blocked_ids);
                            }])->with(['arrived' => function($query)use ($blocked_ids) {
                                $query->whereNotIn('user_id', $blocked_ids);
                            }])
                        ->where('utc_event_end_date', '>', Carbon::now())
                        ->orderBy('utc_event_time', 'desc')->orderBy('distance', 'asc')
                        ->withCount('likes', 'arrived', 'userArrived', 'userLiked')
                        ->take(10)->skip($skip)->get();
        return sendSuccess('', $data);
    }

    function mainEvents() {
        $skip = $_GET['skip'] * 10;
        $filter = '';
        if (isset($_GET['filter'])) {
//            Followers,Interests,Latest
            $filter = $_GET['filter'];
        }
        $user_intrests = UserInterest::select('interest_id')->where('user_id', $this->userId)->get()->toArray();
        $other_intrest = UserInterest::where('user_id', $this->userId)->where('interest_id', 22)->first();
        $follower = UserFollow::select('followed_id')->where(array('user_id' => $this->userId))->get()->toArray();
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        if ($filter == 'Followers') {
            $get_events = Event::select('id')->whereIn('user_id', $follower)->groupBy('id')
                    ->get();
            $ids_ordered = implode(',', $get_events->pluck('id')->toArray());
            if (!$ids_ordered) {
                $ids_ordered = '';
            }
            $data['events'] = Event::selectRaw("*,3956 * 2 * ASIN(SQRT(POWER(SIN(($this->lat - lat) * pi()/180 / 2), 2) + COS($this->lat * pi()/180) * COS(lat * pi()/180) * POWER(SIN(($this->lng - lng) *  pi()/180 / 2), 2) )) as distance,RIGHT(`event_date`,LOCATE(' ',`event_date`) - 2) AS time")
                            ->where('utc_event_end_date', '>', Carbon::now())
                            ->whereNotIn('user_id', $blocked_ids)
                            ->whereHas('user', function ($query) use($follower) {
                                $query->where('is_private', 0)
//                                        ->orWhere('id', $this->userId)
                                ->whereIn('id', $follower);
                            })->orWhereHas('arrived', function($realation) {
                                $realation->where('user_id', $this->userId);
                            })->whereNotIn('user_id', $blocked_ids)
                            ->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'Invites.user', 'interests.interest', 'arrived.user')
                            ->with(['comments' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['likes' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['Invites' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['arrived' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->orderBy('event_date', 'asc')->orderBy('distance', 'asc')
//                                        ->when($ids_ordered, function ($query) use ($ids_ordered) {
//                                return $query->orderByRaw(DB::raw("FIELD(id, $ids_ordered)DESC,utc_event_time,distance"));
//                            }, function ($query) {
//                                return $query->orderBy('distance', 'asc');
//                            })
                                    ->withCount('likes', 'arrived', 'comments', 'userArrived', 'userLiked')
                            ->take(10)->skip($skip)->get();
            return sendSuccess('', $data);
        }
        if ($filter == 'Latest') {
            $data['events'] = Event::selectRaw("*,3956 * 2 * ASIN(SQRT(POWER(SIN(($this->lat - lat) * pi()/180 / 2), 2) + COS($this->lat * pi()/180) * COS(lat * pi()/180) * POWER(SIN(($this->lng - lng) *  pi()/180 / 2), 2) )) as distance,RIGHT(`event_date`,LOCATE(' ',`event_date`) - 2) AS time")
                            ->where('utc_event_end_date', '>', Carbon::now())
                            ->whereNotIn('user_id', $blocked_ids)
                            ->whereHas('user', function ($query) use($follower) {
                                $query->where('is_private', 0)->orWhere('id', $this->userId)
                                ->orWhereIn('id', $follower);
                            })->orWhereHas('arrived', function($realation) {
                                $realation->where('user_id', $this->userId);
                            })->whereNotIn('user_id', $blocked_ids)
                            ->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'Invites.user', 'interests.interest', 'arrived.user')
                            ->with(['comments' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['likes' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['Invites' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['arrived' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])
                            ->withCount('likes', 'arrived', 'userArrived', 'userLiked', 'comments')
//                            ->orderBy('time', 'asc')
                            ->orderBy('event_date', 'asc')
                            ->orderBy('distance', 'asc')
                            ->skip($skip)->take(10)->get();
            return sendSuccess('', $data);
        }

        if ($filter == 'Interest') {
            $get_events = EventIntrest::select('event_id')->whereIn('interest_id', $user_intrests)->groupBy('event_id')
                    ->get();
            $ids_ordered = implode(',', $get_events->pluck('event_id')->toArray());
            if (!$ids_ordered) {
                $ids_ordered = '';
            }
            $ids_to_get = EventIntrest::select('event_id')->whereIn('interest_id', $user_intrests)->groupBy('event_id')
                            ->get()->toArray();
            $data['events'] = Event::selectRaw("*,3956 * 2 * ASIN(SQRT(POWER(SIN(($this->lat - lat) * pi()/180 / 2), 2) + COS($this->lat * pi()/180) * COS(lat * pi()/180) * POWER(SIN(($this->lng - lng) *  pi()/180 / 2), 2) )) as distance")
                            ->where('utc_event_end_date', '>', Carbon::now())
                            ->whereNotIn('user_id', $blocked_ids)
                            ->where('is_private', 0)
                            ->where(function($q) use($ids_to_get, $other_intrest) {
                                $q->whereIn('id', $ids_to_get);
                                $q->when($other_intrest, function ($query) {
                                    return $query->orwhereDoesntHave('interests');
                                });
                            })
                            ->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'Invites.user', 'interests.interest', 'arrived.user')
                            ->with(['comments' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['likes' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['Invites' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['arrived' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])
//                                        ->when($ids_ordered, function ($query) use ($ids_ordered) {
////                                return $query->orderByRaw(DB::raw("FIELD(id, $ids_ordered)DESC,utc_event_time,distance"));
//                                    return $query->orderBy('utc_event_time', 'desc')->orderBy('distance', 'asc');
//                            }, function ($query) {
//                                return $query->orderBy('utc_event_time', 'desc')->orderBy('distance', 'asc');
//                            })
//                            ->orderBy('time', 'asc')
                            ->orderBy('event_date', 'asc')
                            ->orderBy('distance', 'asc')
                                    ->withCount('likes', 'arrived', 'comments', 'userArrived', 'userLiked')
                            ->take(10)->skip($skip)->get();
            return sendSuccess('', $data);
        }
        if ($user_intrests) {
            $get_events = EventIntrest::select('event_id')->whereIn('interest_id', $user_intrests)->groupBy('event_id')
                    ->get();
            $ids_ordered = implode(',', $get_events->pluck('event_id')->toArray());
            if (!$ids_ordered) {
                $ids_ordered = '';
            }
            $ids_to_get = EventIntrest::select('event_id')->whereIn('interest_id', $user_intrests)->groupBy('event_id')
                            ->get()->toArray();
            $data['events'] = Event::selectRaw("*,3956 * 2 * ASIN(SQRT(POWER(SIN(($this->lat - lat) * pi()/180 / 2), 2) + COS($this->lat * pi()/180) * COS(lat * pi()/180) * POWER(SIN(($this->lng - lng) *  pi()/180 / 2), 2) )) as distance")
                            ->where('utc_event_end_date', '>', Carbon::now())
                            ->whereNotIn('user_id', $blocked_ids)
                            ->whereIn('id', $ids_to_get)
                            ->whereHas('user', function ($query) use($follower) {
                                $query->where('is_private', 0)->orWhere('id', $this->userId)
                                ->orWhereIn('id', $follower);
                            })->orWhereHas('arrived', function($realation) {
                                $realation->where('user_id', $this->userId);
                            })->whereNotIn('user_id', $blocked_ids)
                            ->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'Invites.user', 'interests.interest', 'arrived.user')
                            ->with(['comments' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['likes' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['Invites' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['arrived' => function($query) use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->when($ids_ordered, function ($query) use ($ids_ordered) {
//                                return $query->orderByRaw(DB::raw("FIELD(id, $ids_ordered)DESC,utc_event_time,distance"));
                                    return $query->orderBy('event_date', 'desc')->orderBy('distance', 'asc');
                            }, function ($query) {
                                return $query->orderBy('event_date', 'desc')->orderBy('distance', 'asc');
                            })->withCount('likes', 'arrived', 'comments', 'userArrived', 'userLiked')
                            ->take(10)->skip($skip)->get();
//                            Log::info($data);
            return sendSuccess('', $data);
        } else {
//            echo 'asdsad';exit;
            $data['events'] = Event::selectRaw("*,3956 * 2 * ASIN(SQRT(POWER(SIN(($this->lat - lat) * pi()/180 / 2), 2) + COS($this->lat * pi()/180) * COS(lat * pi()/180) * POWER(SIN(($this->lng - lng) *  pi()/180 / 2), 2) )) as distance")
                            ->where('utc_event_end_date', '>', Carbon::now())
                            ->whereNotIn('user_id', $blocked_ids)
                            ->whereHas('user', function ($query) use($follower) {
                                $query->where('is_private', 0)->orWhere('id', $this->userId)
                                ->orWhereIn('id', $follower);
                            })->orWhereHas('arrived', function($realation) {
                                $realation->where('user_id', $this->userId);
                            })->whereNotIn('user_id', $blocked_ids)
                            ->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'Invites.user', 'interests.interest', 'arrived.user')
                            ->with(['comments' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['likes' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['Invites' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])->with(['arrived' => function($query)use ($blocked_ids) {
                                    $query->whereNotIn('user_id', $blocked_ids);
                                }])
                            ->withCount('likes', 'arrived', 'userArrived', 'userLiked', 'comments')
                            ->orderBy('event_date', 'asc')
                            ->orderBy('distance', 'asc')
                            ->skip($skip)->take(10)->get();
        }
        return sendSuccess('', $data);
    }

    function getEvent($id) {
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $event = Event::selectRaw("*,3956 * 2 * ASIN(SQRT(POWER(SIN(($this->lat - lat) * pi()/180 / 2), 2) + COS($this->lat * pi()/180) * COS(lat * pi()/180) * POWER(SIN(($this->lng - lng) *  pi()/180 / 2), 2) )) as distance")
                        ->whereNotIn('user_id', $blocked_ids)->with('user', 'likes.user', 'comments.user', 'attachments', 'interests', 'Invites.user', 'interests.interest', 'arrived.user')
                        ->with(['comments' => function($query)use ($blocked_ids) {
                                $query->whereNotIn('user_id', $blocked_ids);
                            }])->with(['likes' => function($query)use ($blocked_ids) {
                                $query->whereNotIn('user_id', $blocked_ids);
                            }])->with(['Invites' => function($query)use ($blocked_ids) {
                                $query->whereNotIn('user_id', $blocked_ids);
                            }])->with(['arrived' => function($query)use ($blocked_ids) {
                                $query->whereNotIn('user_id', $blocked_ids);
                            }])
                        ->orderBy('utc_event_time', 'desc')->orderBy('distance', 'asc')
                        ->where('id', $id)
                        ->withCount('likes', 'arrived', 'userArrived', 'userLiked')->first();
        $event->view_count = $event->view_count + 1;
        $event->save();
        return sendSuccess('', $event);
    }

    function getCount() {
        $follower = UserFollow::select('followed_id')->where(array('user_id' => $this->userId))->get()->toArray();
        $data['chat_count'] = ChatMessage::where(array('receiver_id' => $this->userId, 'is_read' => 0))->count();
        $data['rsvpd_count'] = EventFollower::where(array('user_id' => $this->userId, 'is_rsvpd' => 1))->count();
        $data['liked_events'] = EventLike::where(array('user_id' => $this->userId))->count();
        $data['followings_count'] = UserFollow::where(array('user_id' => $this->userId))->count();
        $data['follower_count'] = UserFollow::where(array('followed_id' => $this->userId))->count();
        $data['events_count'] = Event::whereHas('user', function ($query)use($follower) {
                    $query->where('is_private', 0)->orWhere('id', $this->userId)->orWhereIn('id', $follower);
                })->count();
        $data['my_events'] = Event::where('user_id', $this->userId)->count();
        return sendSuccess('', $data);
    }

    function incrementPhoneViewCount($event_id) {
        $event = Event::find($event_id);
        if ($event) {
            $event->phone_view_count = $event->phone_view_count + 1;
            $event->save();
            return sendSuccess('Phone view count incremented', $event);
        }
        return sendError('Event Not Found !', 404);
    }

    function incrementWebsiteViewCount($event_id) {
        $event = Event::find($event_id);
        if ($event) {
            $event->website_view_count = $event->website_view_count + 1;
            $event->save();
            return sendSuccess('Website view count incremented', $event);
        }
        return sendError('Event Not Found !', 404);
    }

}

<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Event;
use App\Interest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\UserFollow;
use App\EventLike;
use App\EventFollower;
use App\EventAttachment;
use App\EventComment;
use App\EventShare;
use App\EventIntrest;
use Illuminate\Support\Str;
use App\EventReoccurance;
use App\UserInterest;
use App\Notification;

class AdminController extends Controller {

    private $userId;
    private $user;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            $this->user = Auth::user();
            return $next($request);
        });
    }

    function dashboard() {
        if (Auth::user()->user_type == 0) {
            return redirect('events');
        }
        $data['title'] = 'Admin';
        $data['users'] = User::where('user_type', 0)->count();
        $data['male'] = User::where('user_type', 0)->where('gender', 'male')->count();
        $data['female'] = User::where('user_type', 0)->where('gender', 'female')->count();
        $data['events'] = Event::count();
        $data['upcoming'] = Event::where('event_end_date', '>', Carbon::now())->count();
        return view('dashboard', $data);
    }

    function users() {
        if (Auth::user()->user_type == 0) {
            return redirect('events');
        }
        $data['title'] = 'User';
        $data['users'] = User::where('user_type', 0)->get();
        return view('users', $data);
    }

    function getUserByGender($gender) {
        $data['title'] = 'User';
        if ($gender == 'male')
            $data['maleBreadCrumb'] = 1;
        else if ($gender == 'female')
            $data['femaleBreadCrumb'] = 1;
        $data['users'] = User::where('user_type', 0)->where('gender', $gender)->get();
        $data['allUsers'] = User::where('user_type', 0)->get();
        return view('users', $data);
    }

    function getUserByLoginType($type, $gender = null) {
        $data['title'] = 'User';
        if ($type == 'fb')
            $data['facebookBreadCrumb'] = 1;
        else if ($type == 'twitter')
            $data['twitterBreadCrumb'] = 1;
        $data['users'] = User::where('user_type', 0)->where('login_type', $type)->get();
        $data['allUsers'] = User::where('user_type', 0)->get();
        return view('users', $data);
    }

    function getUserEvents($user_id) {
        $data['user'] = User::find($user_id);
        if ($data['user']) {
            $data['listing_filter'] = 1;
            $data['events'] = Event::where('user_id', $user_id)->orderBy('event_date', 'desc')->get();
            $data['title'] = 'User Events';
            return view('user_events', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getUserEventsPost(Request $request) {
        $user_id = $request->input('user_id');
        $data['user'] = User::find($user_id);
        if ($data['user']) {

            $data['listing_filter'] = $request->input('listing_filter');
            if ($data['listing_filter'] == 1) {
                $data['events'] = Event::where('user_id', $user_id)->orderBy('event_date', 'desc')->get();
            } else if ($data['listing_filter'] == 2) {
                $data['events'] = Event::where('user_id', $user_id)->where('utc_event_end_date', '<', Carbon::now())->orderBy('event_date', 'desc')->get();
            } else if ($data['listing_filter'] == 3) {
                $data['events'] = Event::where('user_id', $user_id)->where('utc_event_time', '<', Carbon::now())->where('utc_event_end_date', '>', Carbon::now())->orderBy('event_date', 'desc')->get();
            } else if ($data['listing_filter'] == 4) {
                $data['events'] = Event::where('user_id', $user_id)->where('utc_event_time', '>', Carbon::now())->orderBy('event_date', 'desc')->get();
            }
            $data['title'] = 'User Events';
            return view('user_events', $data);
        }
        return Redirect::to(URL::previous());
    }

//    function getUserOldEvents($user_id) {
//        $data['user'] = User::find($user_id);
//        if($data['user'])
//        {
//            $data['listing_filter'] = 2;
//            $data['events'] = Event::where('user_id', $user_id)->where('utc_event_end_date', '<', Carbon::now())->orderBy('event_date', 'desc')->get();
//            $data['title'] = 'User Events';
//            return view('user_events', $data);
//        }
//        return Redirect::to(URL::previous());
//    }
//
//    function getUserOngoingEvents($user_id) {
//        $data['user'] = User::find($user_id);
//        if($data['user'])
//        {
//            $data['listing_filter'] = 3;
//            $data['events'] = Event::where('user_id', $user_id)->where('utc_event_time', '<', Carbon::now())->where('utc_event_end_date', '>', Carbon::now())->orderBy('event_date', 'desc')->get();
//            $data['title'] = 'User Events';
//            return view('user_events', $data);
//        }
//        return Redirect::to(URL::previous());
//    }
//
//    function getUserUpcomingEvents($user_id) {
//        $data['user'] = User::find($user_id);
//        if($data['user'])
//        {
//            $data['listing_filter'] = 4;
//            $data['events'] = Event::where('user_id', $user_id)->where('utc_event_time', '>', Carbon::now())->orderBy('event_date', 'desc')->get();
//            $data['title'] = 'User Events';
//            return view('user_events', $data);
//        }
//        return Redirect::to(URL::previous());
//    }

    function editUserEventView($id) {
        $data['title'] = 'Edit User Event';
        $data['event'] = Event::find($id);
        if ($data['event']) {
            $data['attachments'] = EventAttachment::where('event_id', $id)->get();
            $data['editUserEventBreadCrumb'] = 1;
            return view('edit_event', $data);
        }
        return Redirect::to(URL::previous());
    }

    function editEventView($id) {
        $data['title'] = 'Edit Event';
        $data['intrest_list'] = Interest::get();
        $data['event'] = Event::find($id);
        if ($data['event']) {
            $data['attachments'] = EventAttachment::where('event_id', $id)->get();
            $data['intrest_ids'] = EventIntrest::where('event_id', $id)->pluck('interest_id')->toArray();
            $reocer_list = EventReoccurance::where('event_id', $id)->first();

            if (isset($reocer_list->reoccurance) && $reocer_list->reoccurance) {
                $data['reocer_list'] = explode(',', $reocer_list->reoccurance);
            } else {
                $data['reocer_list'] = array();
            }

            $data['editEventBreadCrumb'] = 1;
            return view('edit_event', $data);
        }
        return Redirect::to(URL::previous());
    }

    function editEvent(Request $request) {

        $request->validate([
            'title' => 'required|max:60',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
//            'location' => 'required|max:60',
//            'website_url' => 'required|max:60|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
//            'phone_no' => ['required', 'regex:/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/'],
            'description' => 'required',
            'is_private' => 'required',
        ]);


        $event_id = $request->input('id');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $start = $startDate . ', ' . $startTime;
        $end = $endDate . ', ' . $endTime;
        $start = date('Y-m-d H:i:s', strtotime($start));
        $end = date('Y-m-d H:i:s', strtotime($end));
        $localTimeZone = $request->input('localTimeZone');

        $dateTime = new DateTime($start, new DateTimeZone($localTimeZone));
        $dateTime->setTimeZone(new DateTimeZone('UTC'));
        $startUTC = $dateTime->format('Y-m-d H:i:s');

        $dateTime = new DateTime($end, new DateTimeZone($localTimeZone));
        $dateTime->setTimeZone(new DateTimeZone('UTC'));
        $endUTC = $dateTime->format('Y-m-d H:i:s');

        $event = Event::find($request->input('id'));
        $event->title = $request->input('title');
        $event->event_date = $start;
        $event->event_end_date = $end;
        $event->utc_event_time = $startUTC;
        $event->utc_event_end_date = $endUTC;
        $event->location = $request->input('location');
//        $event->description = utf8_decode($request->input('description'));
        $event->description = $request->input('description');
        $event->is_private = $request->input('is_private');
        $event->lat = $request->input('lat');
        $event->lng = $request->input('lng');
        $event->phone_no = $request->input('phone_no');
        $event->website_url = $request->input('website_url');

        $image = $request->file('image');

        if ($image) {
            $input['imagename'] = 'http://ec2-18-217-46-7.us-east-2.compute.amazonaws.com/public/images/events/eventimage_' . Str::random(15) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('../public/images/events/');
            $image->move($destinationPath, $input['imagename']);
            $event->cover = $input['imagename'];
        }


        $event->is_reoccuring = $request['is_reoccuring'];
        if ($request['reoccure_type']) {
            $event->reoccure_type = $request['reoccure_type'];
        }
        if ($request['reoccure']) {
            $event->reoccure = $request['reoccure'];
        }

        $event->is_reoccuring_forever = ($request['is_reoccuring_forever']) ? $request['is_reoccuring_forever'] : '0';

        if ($request['reoccure_date']) {
            $event->reoccure_end_date = $request['reoccure_date'];
        }
        if ($request['reoccure_end_date']) {
            $event->reoccure_end_date = $request['reoccure_end_date'];
            //$event->utc_reoccure_end_date = date("Y-m-d H:i:s", strtotime($request['utc_reoccure_end_date'] . " $timezone minutes"));
        }
        $event->save();

        EventIntrest::where('event_id', $request->input('id'))->delete();
        if ($request->interest) {
            foreach ($request->interest as $ids) {
                $event_interst = new EventIntrest();
                $event_interst->event_id = $request->input('id');
                $event_interst->interest_id = $ids;
                $event_interst->save();
            }
        }

        EventReoccurance::where('event_id', $event_id)->delete();
        if ($request['is_reoccuring'] == '1') { //save reoccurance
            $reoccurance = new EventReoccurance();
            $reoccurance->user_id = $event->user_id;
            $reoccurance->event_id = $event_id;
            if ($event->reoccure_type == '7') {
                $reoccurance->reoccurance = ($request['days']) ? implode(",", $request['days']) : '';
            } else if ($event->reoccure_type == '360') {
                $reoccurance->reoccurance = ($request['years']) ? implode(",", $request['years']) : '';
            }
            $reoccurance->save();
        }

        $attachments = preg_replace(array('/"/'), '', $request->input('attachments'));

        $attachmentNames = explode(',', $attachments);

        foreach ($attachmentNames as $attachmentName) {
            if ($attachmentName != "") {
                $attachment = new EventAttachment();
                $attachment->attachment_path = $attachmentName;
                $tmp = explode('.', $attachmentName);
                $end = end($tmp);
                if ($end == 'jpg' || $end == 'jpeg' || $end == 'png' || $end == 'bmp')
                    $attachment->type = 'image';
                else if ($end == 'mp4')
                    $attachment->type = 'video';
                $attachment->image_ratio = '0.5625';
                $event->attachments()->save($attachment);
            }
        }

        Session::flash('success', 'Event Updated Successfully');
        if ($request->input('breadCrumb') == 'user')
            return redirect('user_event_details/' . $event->id);
        else if ($request->input('breadCrumb') == 'event')
            return redirect('event_details/' . $event->id);
    }

    function deleteUser($id) {
        User::where('id', $id)->delete();
        Session::flash('success', 'User deleted successfully');
        return Redirect::to(URL::previous());
    }

    function banUser($id) {
        User::where('id', $id)->update(['is_banned' => 1]);
        Session::flash('success', 'User blocked successfully');
        return Redirect::to(URL::previous());
    }

    function removeBanUser($id) {
        User::where('id', $id)->update(['is_banned' => 0]);
        Session::flash('success', 'User unblocked successfully');
        return Redirect::to(URL::previous());
    }

    function getEvents(Request $request) {
        $data['listing_filter'] = $request->input('listing_filter');

        if ($request->input("event_added_by")) {
            $added_by_filter = $request->input("event_added_by");
        }

        if ($data['listing_filter'] == 1) {
            if (isset($added_by_filter)) {
                $data['events'] = Event::where('event_added_by', $added_by_filter)->orderBy('event_date', 'desc')->get();
            } else {
                $data['events'] = Event::orderBy('event_date', 'desc')->get();
            }
        } else if ($data['listing_filter'] == 2) {
            if (isset($added_by_filter)) {
                $data['events'] = Event::where('event_added_by', $added_by_filter)->where('utc_event_end_date', '<', Carbon::now())->orderBy('event_date', 'desc')->get();
            } else {
                $data['events'] = Event::where('utc_event_end_date', '<', Carbon::now())->orderBy('event_date', 'desc')->get();
            }
        } else if ($data['listing_filter'] == 3) {
            if (isset($added_by_filter)) {
                $data['events'] = Event::where('event_added_by', $added_by_filter)->where('utc_event_time', '<', Carbon::now())->where('utc_event_end_date', '>', Carbon::now())->orderBy('event_date', 'desc')->get();
            } else {
                $data['events'] = Event::where('utc_event_time', '<', Carbon::now())->where('utc_event_end_date', '>', Carbon::now())->orderBy('event_date', 'desc')->get();
            }
        } else if ($data['listing_filter'] == 4) {
            if (isset($added_by_filter)) {
                $data['events'] = Event::where('event_added_by', $added_by_filter)->where('utc_event_time', '>', Carbon::now())->orderBy('event_date', 'desc')->get();
            } else {
                $data['events'] = Event::where('utc_event_time', '>', Carbon::now())->orderBy('event_date', 'desc')->get();
            }
        }
        $data['title'] = 'Events';
        if (isset($added_by_filter)) {
            $data["added_by_filter"] = $added_by_filter;
            if ($added_by_filter == "admin") {
                $data["adminEventsBreadCrumb"] = 1;
            }
        }
        return view('events', $data);
    }

    function events() {
        $data['listing_filter'] = 1;
        $data['events'] = Event::where('event_added_by', 'user')->orderBy('event_date', 'desc')->get();
        $data['title'] = 'Events';
        return view('events_pagination', $data);
    }

    function hijinnks_events() {
        $data['listing_filter'] = 1;
        $data['events'] = Event::where(['event_added_by' => 'admin'])->orderBy('event_date', 'desc')->get();
        $data['title'] = 'Hijinks Events';
        $data['adminEventsBreadCrumb'] = 1;
        return view('events_pagination_hijinks', $data);
    }

    function eventsDataTablePagination(Request $request) {

        $columns = array(
            0 => 'sr',
            1 => 'title',
            2 => 'cover photo',
            3 => 'event date',
            4 => 'event time',
            5 => 'location',
            6 => 'members',
            7 => 'rsvp',
            8 => 'likes',
            8 => 'viewed',
            8 => 'actions'
        );

        $totalData = Event::count();
        $totalFiltered = $totalData;
        $limit = ($request->input('length')) ? $request->input('length') : '10';
        $start = $request->input('start');
        $order = '';
//        $order = $columns[($request->input('order.0.column')) ? $request->input('order.0.column') : '0'];
        if ($request->input('order.0.column')) {
            $order = $columns[$request->input('order.0.column')];
            if ($order == 'event date')
                $order = 'event_date';
        } else {
            $order = 'event_date';
        }
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            if ($request->input('event_added_by') && $request->input('event_added_by') == "admin") {

                $events = Event::where('event_added_by', 'admin')->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            } else if ($request->input('event_added_by') && $request->input('event_added_by') == "user") {

                $events = Event::where('event_added_by', 'user')->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            } else {
                $events = Event::offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            }
//            dd($events);
        } else {
            $search = $request->input('search.value');

            if ($request->input('event_added_by') && $request->input('event_added_by') == "admin") {
                $events = Event::where('event_added_by', 'admin')
                        ->where(function($q) {
                            $q->where('title', 'LIKE', "%{$search}%")
                            ->orWhere('location', 'LIKE', "%{$search}%");
                        })->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Event::where('event_added_by', 'admin')
                                ->where(function($q) use($search) {
                                    $q->where('title', 'LIKE', "%{$search}%")
                                    ->orWhere('location', 'LIKE', "%{$search}%");
                                })->count();
            } else if ($request->input('event_added_by') && $request->input('event_added_by') == "user") {
                $events = Event::where('event_added_by', 'user')
                        ->where(function($q)use($search) {
                            $q->where('title', 'LIKE', "%{$search}%")
                            ->orWhere('location', 'LIKE', "%{$search}%");
                        })->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Event::where('event_added_by', 'user')
                                ->where(function($q) use($search) {
                                    $q->where('title', 'LIKE', "%{$search}%")
                                    ->orWhere('location', 'LIKE', "%{$search}%");
                                })->count();
            } else {
                $events = Event::where('title', 'LIKE', "%{$search}%")
                        ->orWhere('location', 'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
                $totalFiltered = Event::where('title', 'LIKE', "%{$search}%")
                        ->orWhere('location', 'LIKE', "%{$search}%")
                        ->count();
            }
        }

        $data = array();
        if (!empty($events)) {
            $i = $start + 1;
            foreach ($events as $event) {

                $nestedData['Sr'] = $i;
                $nestedData['Title'] = '<a href="' . asset('event_details/' . $event->id) . '" class="clickable"><span class="colored">' . $event->title . '</span></a>';

                $image = asset('public/images/events/cover_photo_placeholder.jpg');
                if ($event->cover) {
                    $image = $event->cover;
                }

                $nestedData['Cover Photo'] = '<img alt="Cover Pic" class="profile-pic img-responsive" src="' . $image . '">';


                $startUtcTime = new DateTime($event->utc_event_time);
                $startLocalTime = new DateTime($event->utc_event_time, new DateTimeZone('UTC'));
                $startLocalTime->setTimeZone(new DateTimeZone($request['user_time_zone']));
                $startLocalTime = $startLocalTime->format('F jS Y,h:i A');


                $endUtcTime = new DateTime($event->utc_event_end_date);
                $endLocalTime = new DateTime($event->utc_event_end_date, new DateTimeZone('UTC'));
                $endLocalTime->setTimeZone(new DateTimeZone($request['user_time_zone']));
                $endLocalTime = $endLocalTime->format('F jS Y,h:i A');

                $startLocalTime = explode(',', $startLocalTime);
                $endLocalTime = explode(',', $endLocalTime);

                $startingDate = $startLocalTime[0];
                $endingDate = $endLocalTime[0];

                $startingTime = $startLocalTime[1];
                $endingTime = $endLocalTime[1];

                $nestedData['Event Date'] = $startingDate . ' - ' . $endingDate;

                $nestedData['Event Time'] = $startingTime . ' - ' . $endingTime;

                $nestedData['Location'] = $event->location;

                $invitesUrl = '';
                if ($event->Invites->count() > 0) {
                    $invitesUrl = asset('event_members/' . $event->id);
                } else {
                    $invitesUrl = '#';
                }
                $nestedData['Members'] = '<a class="clickable" href="' . $invitesUrl . '">' . $event->Invites->count() . '</a>';

                $rsvpUrl = '';
                if ($event->arrived->count() > 0) {
                    $rsvpUrl = asset('event_rsvps/' . $event->id);
                } else {
                    $rsvpUrl = '#';
                }
                $nestedData['RSVP'] = '<a class="clickable" href="' . $rsvpUrl . '">' . $event->arrived->count() . '</a>';

                $likesUrl = '';
                if ($event->likes->count() > 0) {
                    $likesUrl = asset('event_liked_by/' . $event->id);
                } else {
                    $likesUrl = '#';
                }
                $nestedData['Likes'] = '<a class="clickable" href="' . $invitesUrl . '">' . $event->likes->count() . '</a>';

                $nestedData['Viewed'] = $event->view_count;

                $nestedData['Actions'] = '<a href="' . asset('edit_event/' . $event->id) . '" title="Edit Event"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a data-target="#delete_modal" href="javascript:void(0)" onclick="deleteModal(' . $event->id . ')" class="btn-popup"><i class="fa fa-trash" aria-hidden="true"></i></a><br>';

                $i++;
                $data[] = $nestedData;
            }
        }

//        if($order == 'type'){
//            $i = 0;
//            foreach($data as $row){
//                $row[$i]['Sr'] = $i;
//                $i++;
//            }
//        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    function deleteEvent($user_id) {
        $event = Event::where('id', $user_id)->first();
        echo '<pre>';
        print_r($event);
        exit;
        Session::flash('success', 'Event Deleted Successfully');
        return Redirect::to(URL::previous());
    }

    function deleteEventAdmin($user_id) {

        $event = Event::where('id', $user_id)->first();
        if (!empty($event)) {
            $event->delete();
        }
        Session::flash('success', 'Event Deleted Successfully');
        return Redirect::to(URL::previous());
    }

    function changePassword() {
        $data['title'] = 'Change Password';
        return view('change_pass', $data);
    }

    function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);
        $password = Auth::user()->password;
        if (Hash::check($request['current_password'], $password)) {
            $newpass = Hash::make($request['password']);
            User::where('id', $this->userId)->update(['password' => $newpass]);
            Session::flash('success', 'Password Updated successfully');
            return Redirect::to(URL::previous());
        } else {
            Session::flash('error', 'Invalid Old Password');
            return Redirect::to(URL::previous());
        }
    }

    function updateName(Request $request) {
        $request->validate([
            'name' => 'required'
        ]);
        User::where('id', $this->userId)->update(['username' => $request->name]);
        Session::flash('success', 'Name Updated successfully');
        return Redirect::to(URL::previous());
    }

    function eventAttachments($event_id) {
        $data['title'] = 'View Attachments';
        $data['attachments'] = EventAttachment::where('event_id', $event_id)->get();
        return view('images', $data);
    }

    function eventDetails($event_id) {
        $data['title'] = 'Event Detail';
        $data['event'] = Event::find($event_id);
        if ($data['event']) {
            $current_user = $data['event']->user_id;
            $all_invited = EventFollower::select('user_id')->where('event_id', $data['event']->id)->get()->toArray();
            $all_invited[] = $current_user;
            $data['users'] = User::whereNotIn('id', $all_invited)->where('user_type', 0)->select('id', 'username')->orderBy('username')->get();
            $data['eventBreadCrumb'] = 1;
            $data['attachments'] = EventAttachment::where('event_id', $event_id)->get();
            return view('event_details', $data);
        }
        return Redirect::to(URL::previous());
    }

    function userEventDetails($event_id) {
        $data['title'] = 'Event Detail';
        $data['event'] = Event::find($event_id);
        if ($data['event']) {
            $data['userBreadCrumb'] = 1;
            $data['attachments'] = EventAttachment::where('event_id', $event_id)->get();
            return view('event_details', $data);
        }
        return Redirect::to(URL::previous());
    }

    function interests() {
        if (Auth::user()->user_type == 0) {
            return redirect('events');
        }
        $data['title'] = 'View Interest';
        $data['intrests'] = Interest::orderBy('title', 'asc')->get();
        return view('intrests', $data);
    }

    function eventsByInterest($intrest_id) {
        $events_id = EventIntrest::select('event_id')->where('interest_id', $intrest_id)->get()->toArray();
        $data['listing_filter'] = 1;
        $data['events'] = Event::whereIn('id', $events_id)->orderBy('event_date', 'desc')->get();
        $data['title'] = 'Events';
        return view('events', $data);
    }

    function usersByInterest($intrest_id) {
        if (Auth::user()->user_type == 0) {
            return redirect('events');
        }
        $users_id = UserInterest::select('user_id')->where('interest_id', $intrest_id)->get()->toArray();
        $data['title'] = 'User';
        $data['users'] = User::whereIn('id', $users_id)->where('user_type', 0)->get();
        return view('users', $data);
    }

//
    function deleteInterest($id) {
        Interest::where('id', $id)->delete();
        Session::flash('success', 'Interest Deleted Successfully');
        return Redirect::to(URL::previous());
    }

    function addInterests(Request $request) {
        $check_intrest = Interest::where('title', $request['title'])->first();
        if ($check_intrest) {
            Session::flash('error', 'Interest Already added');
            return Redirect::to(URL::previous());
        }
        $add_intertest = new Interest;
        $add_intertest->title = $request['title'];
        $add_intertest->save();
        Session::flash('success', 'Interest Added Successfully');
        return Redirect::to(URL::previous());
    }

    function getFollowing($user_id) {
        $data['user'] = User::find($user_id);
        if ($data['user']) {
            $data['title'] = 'Following';
            $following = UserFollow::select('followed_id')->where('user_id', $user_id)->get()->toArray();
            $data['users'] = User::whereIn('id', $following)->get();
            $data['userFollowingBreadCrumb'] = 1;
            return view('users', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getFollowers($user_id) {
        $data['user'] = User::find($user_id);
        if ($data['user']) {
            $data['title'] = 'Followers';
            $following = UserFollow::select('user_id')->where('followed_id', $user_id)->get()->toArray();
            $data['users'] = User::whereIn('id', $following)->get();
            $data['userFollowersBreadCrumb'] = 1;
            return view('users', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getUserEventLikedBy($event_id) {
        $data['event'] = Event::find($event_id);
        if ($data['event']) {
            $data['title'] = 'User\'s Event Liked By';
            $users = EventLike::select('user_id')->where('event_id', $event_id)->get()->toArray();
            $data['users'] = User::whereIn('id', $users)->get();
            $data['userEventLikedByBreadCrumb'] = 1;
            return view('users', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getEventLikedBy($event_id) {
        $data['event'] = Event::find($event_id);
        if ($data['event']) {
            $data['title'] = 'Event Liked By';
            $users = EventLike::select('user_id')->where('event_id', $event_id)->get()->toArray();
            $data['users'] = User::whereIn('id', $users)->get();
            $data['eventLikedByBreadCrumb'] = 1;
            return view('users', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getUserEventRsvps($event_id) {
        $data['event'] = Event::find($event_id);
        if ($data['event']) {

            $data['title'] = 'User\'s Event Rsvps';
            $users = EventFollower::select('user_id')->where('event_id', $event_id)->where('is_rsvpd', 1)->get()->toArray();
            $data['users'] = User::whereIn('id', $users)->get();
            $data['userEventRsvpsBreadCrumb'] = 1;
            return view('users', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getEventRsvps($event_id) {
        $data['event'] = Event::find($event_id);
        if ($data['event']) {
            $data['listing_filter'] = 1;
            $data['title'] = 'Event\'s Rsvps';
            $users = EventFollower::select('user_id')->where('event_id', $event_id)->where('is_rsvpd', 1)->get()->toArray();
            $data['users'] = User::whereIn('id', $users)->get();
            $data['eventRsvpsBreadCrumb'] = 1;
            return view('users', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getUserEventMembers($event_id) {
        $data['event'] = Event::find($event_id);
        if ($data['event']) {
            $data['title'] = 'User\'s Event Members';
            $users = EventFollower::select('user_id')->where('event_id', $event_id)->get()->toArray();
            $data['users'] = User::whereIn('id', $users)->get();
            $data['userEventMembersBreadCrumb'] = 1;
            return view('users', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getEventMembers($event_id) {
        $data['event'] = Event::find($event_id);
        if ($data['event']) {
            $data['title'] = 'Event\'s Members';
            $users = EventFollower::select('user_id')->where('event_id', $event_id)->get()->toArray();
            $data['users'] = User::whereIn('id', $users)->get();
            $data['eventMembersBreadCrumb'] = 1;
            return view('users', $data);
        }
        return Redirect::to(URL::previous());
    }

    function userDetails($id) {
        $data['title'] = 'User';
        $data['user'] = User::find($id);
        return view('user_details', $data);
    }

    function getLikeEvents($user_id) {
        $data['user'] = User::find($user_id);
        if ($data['user']) {
            $data['listing_filter'] = 1;
            $users = EventLike::select('event_id')->where('user_id', $user_id)->get()->toArray();
            $data['events'] = Event::whereIn('id', $users)->get();
            $data['title'] = 'User\'s Liked Events';
            $data['userLikedEventsBreadCrumb'] = 1;
            return view('events', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getRsvpEvents($user_id) {
        $data['user'] = User::find($user_id);
        if ($data['user']) {
            $data['listing_filter'] = 1;
            $users = EventFollower::select('event_id')->where('user_id', $user_id)->where('is_rsvpd', 1)->get()->toArray();
            $data['events'] = Event::whereIn('id', $users)->get();
            $data['userRsvpEventsBreadCrumb'] = 1;
            $data['title'] = 'User\'s Rsvp Events';
            return view('events', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getCommentedEvents($user_id) {
        $data['user'] = User::find($user_id);
        if ($data['user']) {
            $data['listing_filter'] = 1;
            $users = EventComment::select('event_id')->where('user_id', $user_id)->get()->toArray();
            $data['events'] = Event::whereIn('id', $users)->get();
            $data['title'] = 'User\'s Commented Events';
            $data['userCommentedEventsBreadCrumb'] = 1;
            return view('events', $data);
        }
        return Redirect::to(URL::previous());
    }

    function getSharedEvents($user_id) {
        $data['user'] = User::find($user_id);
        if ($data['user']) {
            $data['listing_filter'] = 1;
            $users = EventShare::select('event_id')->where('user_id', $user_id)->get()->toArray();
            $data['events'] = Event::whereIn('id', $users)->get();
            $data['title'] = 'User\'s Shared Events';
            $data['userSharedEventsBreadCrumb'] = 1;
            return view('events', $data);
        }
        return Redirect::to(URL::previous());
    }

    function sendNotificion(Request $request) {
        $message = $request['message'];
        $data['message'] = [];
        foreach (User::all() as $user) {
            sendNotification($user->id, $data, '', $message);
        }
        Session::flash('success', 'Notification Send Successfully');
        return Redirect::to(URL::previous());
    }

    function showNotificion() {
        if (Auth::user()->user_type == 0) {
            return redirect('events');
        }
        $data['title'] = 'Send Notification';
        return view('notification', $data);
    }

    function eventsMap() {
        $data['listing_filter'] = 1;
        $data['events'] = Event::all();
        $data['title'] = 'Events Map';
        return view('events_map', $data);
    }

    function usersMap() {
        $data['users'] = User::where('user_type', 0)->get();
        $data['title'] = 'Users Map';
        return view('user_map', $data);
    }

    function csvUpload() {
        set_time_limit(0);
        if (($handle = fopen(public_path() . '/csv/Paris_sports.csv', 'r')) !== FALSE) {
            $i = 0;
//           while (($data = fgetcsv($handle, 100000, ',')) !== FALSE) {
//               echo '<pre>';
//               print_r($data);
//           }exit;
            while (($data = fgetcsv($handle, 1000000, ',')) !== FALSE) {
//echo '<pre>';
//                print_r($data);exit;
                if ($i > 0) {
//                    echo 'asdad';
//                 $check_intrest = Interest::where('title', $data[3])->first();
//                    echo '<pre>';
//                print_r($data);exit;
//                    $intrests = explode('&', $data[3]);
//                 print_r($intrests);exit;
//                    echo '<pre>';
//                print_r($data);exit;
//                    $address = $data[6]; // Google HQ
//                    $prepAddr = str_replace(' ', '+', $address);
//                    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false&key=AIzaSyCg-Yy6QiJ8peKChJSM-c2yvHehQznwZ6M');
//                    $output = json_decode($geocode);
//                    echo '<pre>';
//                    print_r($output);exit;
//                    if (isset($output->results[0]->geometry->location->lat)) {
//                        echo 'asdas';
//                       echo $effectiveDate = date('Y-m-d', strtotime("+3 months", strtotime(date('Y-m-d'))));
//                        $timestamp = strtotime($effectiveDate.' '.$data[3]);
//                        echo '<pre>';
//                    print_r($timestamp);exit;
//                        $lat = $output->results[0]->geometry->location->lat;
//                        $lng = $output->results[0]->geometry->location->lng;
//
//                        $curl_url = "https://maps.googleapis.com/maps/api/timezone/json?location=$lat,$lng&timestamp=$timestamp&key=AIzaSyDdxlXEZmkr-7RJsFN7wqX5bJpBUTfzhxk";
//                        $ch = curl_init();
//                        curl_setopt($ch, CURLOPT_URL, $curl_url);
//                        curl_setopt($ch, CURLOPT_HEADER, false);
//                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                        $response = json_decode(curl_exec($ch));
//                        curl_close($ch);
//                        $sign = "-";
//                        $GMT_hours = "+00";
//
//                        $gmtTime = (abs($response->rawOffset) - $response->dstOffset);
//
//                        if ($response->status == 'OK') {
//                            if (strpos($response->rawOffset, '-') !== FALSE)
//                                $sign = "+";
//                            $GMT_hours = $sign . ($gmtTime / 60);
//                        }
//                        $timezone = $GMT_hours;


                    if (strpos($data[10], '/') !== false) {
                        $lat_long = explode('/', $data[10]);
                    } elseif (strpos($data[10], ',') !== false) {
                        $lat_long = explode(',', $data[10]);
                    } elseif (strpos($data[10], '-') !== false) {
                        $lat_long = explode('-', $data[10]);
                    }else {
                        $lat_long = '';
                    }

                
                    $timezone = 480;
//                    st, nd, rd or th.
                    $replaceded_itmes = array('ST', 'ND', 'RD', 'TH');
                    $start_date = str_replace($replaceded_itmes, "", $data[6]);
                    $end_date = str_replace($replaceded_itmes, "", $data[7]);
                    $effectiveDate = date('Y-m-d', strtotime($start_date));
                    $start_data = date("Y-m-d H:i:s", strtotime($effectiveDate . ' ' . $data[4]));
                    $utc_start_date = date("Y-m-d H:i:s", strtotime($start_data . " $timezone minutes"));
                    $effectiveDateEnd = date('Y-m-d', strtotime($end_date));
                    $end_data = date("Y-m-d H:i:s", strtotime($effectiveDateEnd . ' ' . $data[5]));
//                        if($start_data > $end_data){
//                           $end_data =  date('Y-m-d H:i:s', strtotime($end_data. ' + 1 days'));
//                        }
                    $utc_end_data = date("Y-m-d H:i:s", strtotime($end_data . " $timezone minutes"));
//                        $user_name = $data[0];
                    $user = User::where('username', 'PARIS')->first();
//
                   
                    if (!$user) {
                        $user = new User;
                        $user->username = 'PARIS';
                        $user->gender = 'male';
                        $user->is_banned = 0;
                        $user->is_verified = 1;
                        $user->save();
                    }
                    $is_reoccuring = 1;
                    $recoure_type = 7;
                    $is_reoccuring_forever = 1;
                    if ($data[8] == 'EVERYDAY' || $data[8] == 'Everyday') {
                        $recoure_type = 1;
                    }
                    if ($data[8] == 'NO'|| $data[8] == 'No') {
                        $recoure_type = 0;
                        $is_reoccuring = 0;
                        $is_reoccuring_forever = 0;
                    }
                   
                    $add_event = Event::where(array('title' => utf8_encode($data[1]), 'description' => utf8_decode($data[2]), 'event_date' => $start_data, 'event_end_date' => $end_data,'days_recoccur'=>$data[8]))->first();
                    if (!$add_event && $data[1]) {
//                    print_r($lat_long);
//                    exit;
//                    if ($data[1]) {
                        $add_event = new Event;
                        $add_event->user_id = $user->id;
                       
//                        $add_event->cover = '';
                        $add_event->cover = 'http://ec2-18-217-46-7.us-east-2.compute.amazonaws.com/public/images/events/' . $data[0] ;
                        
                        $add_event->is_private = 0;
                        $add_event->location = $data[9];
                     
                        $long = isset($lat_long[1]) ? substr($lat_long[1], 0, -1) : 0;
                        $lat = isset($lat_long[0]) ? substr($lat_long[0], 0, -1) : 0;
                        $add_event->lat = $lat; // substr($lat_long[0], 1);
                        $add_event->lng = $long; //$lat_long; 

                        $add_event->timezone = -($timezone / 60);
                        $add_event->event_date = $start_data;
                        $add_event->utc_event_time = $utc_start_date;
                        $add_event->event_end_date = $end_data;
                        $add_event->utc_event_end_date = $utc_end_data;
                        $add_event->reoccure = 0;
                        $add_event->is_reoccuring = $is_reoccuring;
                        $add_event->days_recoccur = $data[8];
                        $add_event->is_reoccuring_forever = $is_reoccuring_forever;
                        $add_event->reoccure_type = $recoure_type;
                        $add_event->utc_reoccure_end_date = $utc_end_data;
                        $add_event->view_count = 0;
                        $add_event->phone_no = isset($data[12]) ? $data[12] : '0';
                        $add_event->website_url = isset($data[11]) ? utf8_encode($data[11]) : '0';
                        $add_event->title = utf8_encode(str_replace("'",'',$data[1]));
                        $add_event->description = isset($data[2]) ? utf8_decode(str_replace("'",'',$data[2])) : '0';
                        $add_event->save();
//                    }
                        if ($data['8']) { //save reoccurance
                            $reoccurance = new EventReoccurance;
                            $reoccurance->user_id = $user->id;
                            $reoccurance->event_id = $add_event->id;
                            $reoccurance->reoccurance = $data['8'] == 'Everyday' ? Null : $data['8'];
                            $reoccurance->save();
                        }
                    
                

                $check_intrest = Interest::where('title', $data[3])->first();
                if ($check_intrest) {
                    $check_intrest_added = EventIntrest::where(array('event_id' => $add_event->id, 'interest_id' => $check_intrest->id))->first();
                    if (!$check_intrest_added) {
                        $event_interst = new EventIntrest();
                        $event_interst->event_id = $add_event->id;
                        $event_interst->interest_id = $check_intrest->id;
                        $event_interst->save();
//        }
                    }
                }}}
                $i++;                
            }exit;
            fclose($handle);
        }
    }

    function csvUpdateTags() {
        if (($handle = fopen(public_path() . '/csv/tags.csv', 'r')) !== FALSE) {
            $i = 0;

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                
            }
            fclose($handle);
        }
    }

    public function deleteEventCoverPic(Request $request) {
        $event = Event::find($request->event_id);
        $event->cover = null;
        $event->save();
        return Response::json(array('status' => 'success'), 200);
    }

    public function deleteAttachment(Request $request) {
        EventAttachment::where('id', $request->attachment_id)->delete();
    }

    public function saveAttachment(Request $request) {
        $event_id = $request->input('event_id');
        $attachment_name = $request->input('attachmentName');

        $attachment = new EventAttachment();
        $attachmentName = explode('""', trim($attachment_name, '"'));
        $attachment->event_id = $event_id;

        $attachment_name = $attachmentName[0];
        $attachment->attachment_path = $attachment_name;
        $tmp = explode('.', $attachment_name);
        $end = end($tmp);
        if ($end == 'jpg' || $end == 'jpeg' || $end == 'png' || $end == 'bmp')
            $attachment->type = 'image';
        else if ($end == 'mp4')
            $attachment->type = 'video';
        $attachment->image_ratio = '0.5625';
        $attachment->save();
        return response()->json($attachment->id);
    }

    function inviteUsers(Request $request) {
        $invites_ids = $request->user_id;
        $event = Event::find($request->id);
        if ($invites_ids) {
            foreach ($invites_ids as $followers) {
                $check_event_followers = EventFollower::where(array('event_id' => $request->id, 'user_id' => $followers))->first();
                if (!$check_event_followers) {
                    $add_followrs = new EventFollower;
                    $add_followrs->event_id = $request->id;
                    $add_followrs->user_id = $followers;
                    $add_followrs->save();
                    $messagex = "You're invited to " . $event->title . " event";
                    //Save notification data
                    $notification = new Notification;
                    $notification->user_id = $event->user_id;
                    $notification->on_user = $followers;
                    $notification->event_id = $request->id;
                    $notification->notification_text = $messagex;
                    $notification->activity_text = 'You invited a user in event.';
                    $notification->type = 'Invite';
                    $notification->save();
                    $data['event_id'] = $request->id;
                    $data['event'] = $event;
                    //Send notification
//                    Log::info();
                    sendNotification($followers, $data, '', $messagex);
                }
            }
            Session::flash('success', 'Users invited successfully');
            return Redirect::to(URL::previous());
        } else {
            Session::flash('error', 'No user selected');
            return Redirect::to(URL::previous());
        }
    }

    function addEventAdminView(Request $request) {

        $data['title'] = 'Add Event';
        $data['intrest_list'] = Interest::get();
        $data['event'] = new Event;
        $data['reocer_list'] = array();
        $data["attachments"] = [];
        $data["addEventAdminBreadCrumb"] = 1;

        return view('add_event_admin', $data);
    }

    function addEventAdmin(Request $request) {

        $request->validate([
            'title' => 'required|max:60',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
//            'location' => 'required|max:60',
//            'website_url' => 'required|max:60|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
//            'phone_no' => ['required', 'regex:/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/'],
            'description' => 'required',
            'is_private' => 'required',
        ]);

        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $start = $startDate . ', ' . $startTime;
        $end = $endDate . ', ' . $endTime;
        $start = date('Y-m-d H:i:s', strtotime($start));
        $end = date('Y-m-d H:i:s', strtotime($end));
        $localTimeZone = $request->input('localTimeZone');

        $dateTime = new DateTime($start, new DateTimeZone($localTimeZone));
        $dateTime->setTimeZone(new DateTimeZone('UTC'));
        $startUTC = $dateTime->format('Y-m-d H:i:s');

        $dateTime = new DateTime($end, new DateTimeZone($localTimeZone));
        $dateTime->setTimeZone(new DateTimeZone('UTC'));
        $endUTC = $dateTime->format('Y-m-d H:i:s');

        $event = new Event;
        $event->title = $request->input('title');
        $event->user_id = $this->userId;
        $event->event_date = $start;
        $event->event_end_date = $end;
        $event->utc_event_time = $startUTC;
        $event->utc_event_end_date = $endUTC;
        $event->location = $request->input('location');
        // SET USER TIMEZONE
        $event->timezone = -8;
//        $event->description = utf8_decode($request->input('description'));
        $event->description = $request->input('description');
        $event->is_private = $request->input('is_private');

        //Uncomment the commented lat and long after paid api is added 
        $event->lat = 36.117453;    //$request->input('lat');
        $event->lng = -115.176688;  //$request->input('lng');

        $event->event_added_by = 'admin';
        $event->phone_no = $request->input('phone_no');
        $event->website_url = $request->input('website_url');

        $image = $request->file('image');

        if ($image) {
            $input['imagename'] = 'http://ec2-18-217-46-7.us-east-2.compute.amazonaws.com/public/images/events/eventimage_' . Str::random(15) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('../public/images/events/');
            $image->move($destinationPath, $input['imagename']);
            $event->cover = $input['imagename'];
        }


        $event->is_reoccuring = $request['is_reoccuring'];
        if ($request['reoccure_type'])
            $event->reoccure_type = $request['reoccure_type'];

        if ($request['reoccure'])
            $event->reoccure = $request['reoccure'];


        $event->is_reoccuring_forever = ($request['is_reoccuring_forever']) ? $request['is_reoccuring_forever'] : '0';

        if ($request['reoccure_date'])
            $event->reoccure_end_date = $request['reoccure_date'];

        if ($request['reoccure_end_date'])
            $event->reoccure_end_date = $request['reoccure_end_date'];
        //$event->utc_reoccure_end_date = date("Y-m-d H:i:s", strtotime($request['utc_reoccure_end_date'] . " $timezone minutes"));
        // echo '<pre>';
        // print_r($event);
        // exit;

        if ($event->save()) {
            if ($request->interest) {
                foreach ($request->interest as $ids) {
                    $event_interst = new EventIntrest();
                    $event_interst->event_id = $event->id;
                    $event_interst->interest_id = $ids;
                    $event_interst->save();
                }
            }

            if ($request['is_reoccuring'] == '1') { //save reoccurance
                $reoccurance = new EventReoccurance();
                $reoccurance->user_id = $event->user_id;
                $reoccurance->event_id = $event->id;
                if ($event->reoccure_type == '2') {
                    $reoccurance->reoccurance = ($request['days']) ? implode(",", $request['days']) : '';
                } else if ($event->reoccure_type == '4') {
                    $reoccurance->reoccurance = ($request['years']) ? implode(",", $request['years']) : '';
                }
                $reoccurance->save();
            }

            $attachments = preg_replace(array('/"/'), '', $request->input('attachments'));

            $attachmentNames = explode(',', $attachments);

            foreach ($attachmentNames as $attachmentName) {
                if ($attachmentName != "") {
                    $attachment = new EventAttachment();
                    $attachment->attachment_path = $attachmentName;
                    $tmp = explode('.', $attachmentName);
                    $end = end($tmp);
                    if ($end == 'jpg' || $end == 'jpeg' || $end == 'png' || $end == 'bmp')
                        $attachment->type = 'image';
                    else if ($end == 'mp4')
                        $attachment->type = 'video';
                    $attachment->image_ratio = '0.5625';
                    $event->attachments()->save($attachment);
                }
            }


            Session::flash('success', 'Event Updated Successfully');
            if ($request->input('breadCrumb') == 'user')
                return redirect('user_event_details/' . $event->id);
            else if ($request->input('breadCrumb') == 'hijinks_event')
                return redirect('event_details/' . $event->id);
        }else {
            print_r("Error in saving record");
            exit;
        }
    }

    function changeUserImage(Request $request) {
        if ($request['file']) {
            $photo_name = time() . '.' . $request->file->getClientOriginalExtension();
            $request->file->move('public/images/profile_pics', $photo_name);
            $photo_name = asset('public/images/profile_pics/' . $photo_name);
            $user = User::find($request->user_id);
            $user->photo = $photo_name;
            $user->save();
        }
        Session::flash('success', 'Updated successfully');
        return Redirect::to(URL::previous());
    }

}

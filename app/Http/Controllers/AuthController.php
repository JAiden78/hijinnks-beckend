<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use App\Event;
use App\EventAttachment;
use App\EventComment;
use App\EventFollower;
use App\EventIntrest;
use App\EventLike;
use Carbon\Carbon;
use App\EventReoccurance;

class AuthController extends Controller {

    function postRegister(Request $request) {
        $validation = $this->validate($request, [
            'email' => 'required|email|max:191',
            'password' => 'required',
            'username' => 'required|max:191',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'device_type' => 'required',
            'device_id' => 'required',
            'time_zone' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 405);
        }

        $email_check = User::where('email', $request['email'])->first();
        if ($email_check) {
            if ($email_check->fb_id != NULL || $email_check->twitter_id != NULL) {
                if ($request['pic']) {
                    $photo_name = time() . '.' . $request->pic->getClientOriginalExtension();
                    $request->pic->move('public/images/profile_pics', $photo_name);
                    $photo_name = asset('public/images/profile_pics/' . $photo_name);
                } else {
                    $photo_name = $email_check->photo;
                }

                if ($request['lat']) {
                    $email_check->lat = $request['lat'];
                }
                if ($request['lng']) {
                    $email_check->lng = $request['lng'];
                }

                $email_check->photo = $photo_name;
                if ($request['location']) {
                    $email_check->location = $request['location'];
                }

                $email_check->device_type = $request['device_type'];
                $email_check->time_zone = $request['time_zone'];
                $email_check->device_id = $request['device_id'];
                $email_check->password = bcrypt($request['password']);

                $email_check->session_token = bcrypt(time());
                $email_check->is_verified = 1;
                $email_check->save();
                Mail::send('email.register', [], function ($m) use ($email_check) {
                    $m->from('Support@hijinnksapp.com', 'Hijinnks App');
                    $m->to($email_check->email, $email_check->username)->subject('Welcome to Hijinnks');
                });
                return sendSuccess('', $email_check);
            } else {
                return sendError("Email already exist.", 406);
            }
        }

        $add_user = new User;
        if ($request['pic']) {
            $photo_name = time() . '.' . $request->pic->getClientOriginalExtension();
            $request->pic->move('public/images/profile_pics', $photo_name);
            $photo_name = asset('public/images/profile_pics/' . $photo_name);
        } else {
            $photo_name = asset('public/images/profile_pics/demo.png');
        }
        $add_user->email = $request['email'];
        $add_user->username = $request['username'];
        if ($request['lat']) {
            $add_user->lat = $request['lat'];
        }
        if ($request['lng']) {
            $add_user->lng = $request['lng'];
        }

        $add_user->photo = $photo_name;
        if ($request['location']) {
            $add_user->location = $request['location'];
        }

        $add_user->device_type = $request['device_type'];
        $add_user->time_zone = $request['time_zone'];
        $add_user->device_id = $request['device_id'];
        $add_user->password = bcrypt($request['password']);
        $add_user->session_token = bcrypt(time());
        $email_check->description = ' ';
        $add_user->is_verified = 0;
        $add_user->save();
//        Mail::send('email.verify_email', [], function ($m) use ($email_check) {
//            $m->from('Support@hijinnksapp.com', 'Hijinnks App');
//            $m->to($email_check->email, $email_check->username)->subject('Welcome to Hijinnks');
//        });
        return sendSuccess('', $add_user);
    }

    function postLogin(Request $request) {
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            if (Auth::user()->is_banned == 1) {
                return sendError('You Are Blocked By Admin', 411);
            }
            $user = User::find(Auth::user()->id);
            $user->device_type = $request['device_type'];
            $user->time_zone = $request['time_zone'];
            $user->device_id = $request['device_id'];
            $user->session_token = bcrypt(time());
            $user->save();
            return sendSuccess('Login Successfully', $user);
        } else {
            return sendError('Invalid Email Or Password', 411);
        }
    }

    function fbLogin(Request $request) {

        $validation = $this->validate($request, [
//            'email' => 'required|email|max:191',
            'username' => 'required|max:191',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'device_type' => 'required',
            'device_id' => 'required',
            'time_zone' => 'required',
            'fb_id' => 'required',
        ]);
        if (!$validation) {

            return sendError($validation, 405);
        }
        $check_login = User::where('fb_id', $request['fb_id'])->first();
        if ($check_login) {
            if ($request['pic']) {
//                $check_login->photo = $request['pic'];
            }
            $check_login->lat = $request['lat'];
            $check_login->lng = $request['lng'];
            if ($request['gender']) {
                $check_login->gender = $request['gender'];
            }
            $check_login->save();
            return sendSuccess('', User::where('fb_id', $request['fb_id'])->first());
        } else {
            $check_user = '';
            $add_user = new User;
            if ($request['email']) {
                $check_user = User::where('email', $request['email'])->first();
                if ($check_user) {
                    if ($request['lat']) {
                        $check_user->lat = $request['lat'];
                    }
                    if ($request['lng']) {
                        $check_user->lng = $request['lng'];
                    }
                    if ($request['location']) {
                        $check_user->location = $request['location'];
                    }
                    $check_user->session_token = bcrypt(time());
                    $check_user->fb_id = $request['fb_id'];
                    $check_user->save();
                    return sendSuccess('', User::find($check_user->id));
                } else {
                    $password = $this->generateRandomString(8);
                    $add_user->password = bcrypt($password);
                }
            }
            $add_user->email = $request['email'];
            $add_user->username = $request['username'];
            if ($request['pic']) {
                $add_user->photo = $request['pic'];
            }
            if ($request['lat']) {
                $add_user->lat = $request['lat'];
            }
            if ($request['lng']) {
                $add_user->lng = $request['lng'];
            }
            if ($request['location']) {
                $add_user->location = $request['location'];
            }
            if ($request['gender']) {
                $add_user->gender = $request['gender'];
            }
            $add_user->device_type = $request['device_type'];
            $add_user->time_zone = $request['time_zone'];
            $add_user->device_id = $request['device_id'];
            $add_user->fb_id = $request['fb_id'];
            $add_user->login_type = 'fb';
            $add_user->session_token = bcrypt(time());
            $add_user->description = ' ';
            $add_user->is_verified = 1;
            $add_user->save();
            if (!$check_user) {
                $data['password'] = $password;
                if ($request['email']) {
                    Mail::send('emails.register', $data, function ($m) use ($add_user) {
                        $m->from('Support@hijinnksapp.com', 'Hijinnks App');
                        $m->to($add_user->email, $add_user->username)->subject('Welcome to Hijinnks');
                    });
                }
            }
            return sendSuccess('', User::find($add_user->id));
        }
    }

    function twitterLogin(Request $request) {

        $validation = $this->validate($request, [
//            'email' => 'required|email|max:191',
            'username' => 'required|max:191',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'device_type' => 'required',
            'device_id' => 'required',
            'time_zone' => 'required',
            'twitter_id' => 'required',
        ]);
        if (!$validation) {
            return sendError($validation, 405);
        }
        $check_login = User::where('twitter_id', $request['twitter_id'])->first();
        if ($check_login) {
            if ($request['pic']) {
                $check_login->photo = $request['pic'];
            }
            if ($request['gender']) {
                $check_login->gender = $request['gender'];
            }
            $check_login->lat = $request['lat'];
            $check_login->lng = $request['lng'];
            $check_login->session_token = bcrypt(time());
            $check_login->save();
            return sendSuccess('', User::where('twitter_id', $request['twitter_id'])->first());
        } else {
            $check_user = '';
            $add_user = new User;
            if ($request['email']) {
                $check_user = User::where('email', $request['email'])->first();
                if ($check_user) {
                    if ($request['lat']) {
                        $check_user->lat = $request['lat'];
                    }
                    if ($request['lng']) {
                        $check_user->lng = $request['lng'];
                    }
                    if ($request['location']) {
                        $check_user->location = $request['location'];
                    }

                    $check_user->session_token = bcrypt(time());
                    $check_user->twitter_id = $request['twitter_id'];
                    $check_user->save();
                    return sendSuccess('', User::find($check_user->id));
                } else {
                    $password = $this->generateRandomString(8);
                    $add_user->password = bcrypt($password);
                }
            }
            if ($request['gender']) {
                $add_user->gender = $request['gender'];
            }
            $add_user->email = $request['email'];
            $add_user->username = $request['username'];
            if ($request['pic']) {
                $add_user->photo = $request['pic'];
            }
            if ($request['lat']) {
                $add_user->lat = $request['lat'];
            }
            if ($request['lng']) {
                $add_user->lng = $request['lng'];
            }
            if ($request['location']) {
                $add_user->location = $request['location'];
            }
            $password = $this->generateRandomString(8);
            $add_user->password = bcrypt($password);
            $add_user->device_type = $request['device_type'];
            $add_user->time_zone = $request['time_zone'];
            $add_user->device_id = $request['device_id'];
            $add_user->twitter_id = $request['twitter_id'];
            $add_user->login_type = 'twitter';
            $add_user->description = ' ';
            $add_user->session_token = bcrypt(time());
            $add_user->is_verified = 1;
            $add_user->save();
            if (!$check_user) {
                $data['password'] = $password;
                if ($request['email']) {
                    Mail::send('emails.register', $data, function ($m) use ($add_user) {
                        $m->from('Support@hijinnksapp.com', 'Hijinnks App');
                        $m->to($add_user->email, $add_user->username)->subject('Welcome to Hijinnks');
                    });
                }
            }
            return sendSuccess('', User::find($add_user->id));
        }
    }

    function forgetPasswordMail(Request $request) {

        if (isset($request['email'])) {
            $check_email = User::where('email', $request['email'])->first();
            if ($check_email) {
                $code = $this->generateRandomString(8);
                $check_email->password = bcrypt($code);
                $check_email->save();
                $viewData['name'] = $check_email->first_name;
                $viewData['password'] = $code;
//                $viewData['url'] = asset('verify-email-token/' . sha1($code));

                Mail::send('emails.forgetmail', $viewData, function ($m) use ($check_email) {
                    $m->from('Support@hijinnks.com', 'Hijinnks App');
                    $m->to($check_email->email, $check_email->username)->subject('Your New Password');
                });
                return sendSuccess("Please check email for new password.", '');
            } else {
                return sendError("Email Not Found", 409);
            }
        } else {
            return sendError("Email Is Required", 408);
        }
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOP!@#$';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function adminLogin(Request $request) {
//        echo '<pre>';
//        print_r(User::where('email',$request['email'])->first());
//        print_r($request->all());exit;
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'is_banned' => 0])) {

            if (Auth::user()->user_type == 1) {
                return Redirect::to(Url::previous());
            } else {
                if (Event::where('user_id', Auth::user()->id)->count() > 0) {
                    return Redirect::to(Url::previous());
                } else {
                    Auth::logout();
                    Session::flash('error', "Please Add Event To Continue to Web Access");
                    return Redirect::to(Url::previous());
                }
            }
        }
        Session::flash('error', "Invalid Login");
        return Redirect::to(Url::previous());
    }

    function reOccurEvent() {
        $events = Event::where(function($like_query) {
                    $like_query->where('is_reoccuring', 1);
//                    
                })->where(function($like_query) {
                    $like_query->where('utc_event_end_date', '>', Carbon::now())
                    ->orWhere('is_reoccuring_forever', '1');
                })
                ->get();
//        echo '<pre>';
//        print_r(Event::doesntHave('Reoccurance')->where('reoccure_type',7)->get());
//        exit;
//                echo date('D');exit;
        foreach ($events as $event) {
            
//            echo $event->id.'<br>';
//            if($event->id == '2799'){
//                
//                $old_check = Carbon::parse($date[0]);
//                echo 'old date: '.  Carbon::parse($date[0]).'<br>';
//                echo 'current date: '. Carbon::now().'<br>';
//                echo 'diff date: '. Carbon::now()->diffInDays($old_check).'<br>';exit;
//            }
//                        echo 'asdasdsad';exit;
//                    }
//            if ($event->Reoccurance) {
                
                if ($event->reoccure_type == 7) {
                    $days = explode(',', $event->Reoccurance->reoccurance);
                $date = explode(' ',$event->event_date);
                $old = Carbon::parse($date[0]);
                $days_to_add = Carbon::now()->diffInDays($old);
                    for ($x = 0; $x <= 6; $x++) {
                         
                        $check_day = date('D', strtotime(date('D') . " + $x days"));
                        if (in_array($check_day, $days)) {
                            
//                            echo $x.'<br>';
//                         echo '--------------------- <br>';
//                            echo 'days_to_add : '.$x.'<br>';
//                            echo 'added_date : '.$old->addDay($days_to_add+$x).'<br>';
//                            echo '--------------------------------------------'.'<br>';
                            $cron_time = date('Y-m-d', strtotime($event->event_date . " + $days_to_add days"));
                        if ($cron_time == date('Y-m-d')) {
//                             if($event->id == '2799'){
                            $this->rePost($event, $x + $days_to_add);
                            $event->delete();
//                            echo  $event->id.'<br>';
//                            echo  $x.'<br>';
                            break;
                             }
//                        }
//                        }
                    }
                }
            }
            if ($event->reoccure_type == 1) {
                $this->rePost($event, 1);
                $event->delete();
            }
//            if ($event->reoccure_type == 7) {
//                $cron_time = date('Y-m-d', strtotime($event->event_date . ' + 7 days'));
//                if ($cron_time == date('Y-m-d')) {
//                    $this->rePost($event, 7);
//                    $event->delete();
//                }
//            }
            if ($event->reoccure_type == 30) {
                $cron_time = date('Y-m-d', strtotime($event->event_date . ' + 30 days'));
                if ($cron_time == date('Y-m-d')) {
                    $this->rePost($event, 30);
                    $event->delete();
                }
            }
            if ($event->reoccure_type == 360) {
                $cron_time = date('Y-m-d', strtotime($event->event_date . ' + 365 days'));
                if ($cron_time == date('Y-m-d')) {
                    $this->rePost($event, 365);
                    $event->delete();
                }
            }
        }
    }

    protected function rePost($post, $addDay) {
        $newPost = $post->replicate();
        $newPost->reoccure = $post->reoccure - 1;
        $old_date = Carbon::parse($post->event_date);
        $old_date_utc = Carbon::parse($post->utc_event_time);

//        $old_date_end = Carbon::parse($post->event_end_date);
//        $old_date_utc_end = Carbon::parse($post->utc_event_end_time);
        $newPost->event_date = $old_date->addDay($addDay);
        $newPost->utc_event_time = $old_date_utc->addDay($addDay);
//        $newPost->event_end_date = $old_date_end->addDay($addDay);
//        $newPost->utc_event_end_date = $old_date_utc_end->addDay($addDay);
        $newPost->save();
        $post->reoccure = 0;
        $post->save();
        $event_attacments = EventAttachment::where('event_id', $post->id)->get();
        foreach ($event_attacments as $attachment) {
            $new_attachment = $attachment->replicate();
            $new_attachment->event_id = $newPost->id;
            $new_attachment->save();
        }

//        $event_comments = EventComment::where('event_id', $post->id)->get();
//        foreach ($event_comments as $comment) {
//            $new_comment = $comment->replicate();
//            $new_comment->event_id = $newPost->id;
//            $new_comment->save();
//        }

//        $event_followers = EventFollower::where('event_id', $post->id)->get();
//        foreach ($event_followers as $follower) {
//            $new_follower = $follower->replicate();
//            $new_follower->event_id = $newPost->id;
//            $new_follower->save();
//        }

        $event_intrests = EventIntrest::where('event_id', $post->id)->get();
        foreach ($event_intrests as $intrest) {
            $new_intrest = $intrest->replicate();
            $new_intrest->event_id = $newPost->id;
            $new_intrest->save();
        }
        $event_reoccurences = EventReoccurance::where('event_id', $post->id)->get();
        foreach ($event_reoccurences as $event_reoccurence) {
            $new_reoccurence = $event_reoccurence->replicate();
            $new_reoccurence->event_id = $newPost->id;
            $new_reoccurence->save();
        }

//        $event_likes = EventLike::where('event_id', $post->id)->get();
//        foreach ($event_likes as $like) {
//            $new_like = $like->replicate();
//            $new_like->event_id = $newPost->id;
//            $new_like->save();
//        }
    }

}

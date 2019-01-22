<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
//Models
use App\BlockUser;
use App\Event;
use App\EventFollower;
use App\EventIntrest;
use App\EventAttachment;
use App\EventReoccurance;
use App\Notification;
use Carbon\Carbon;
class EventsController extends Controller {

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

    function addEvent(Request $request) {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $validation = $this->validate($request, [
            'event_date' => 'required',
            'title' => 'required',
//            'description' => 'required',
            'location' => 'required',
//            'phone_no' => 'required',
//            'website_url' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'is_private' => 'required',
            'is_reoccuring' => 'required'
        ]);

        if (!$validation) {
            return Response::json(array('status' => 'error', 'errorMessage' => $validation));
        }
//
//        \Log::info("-----new Data-----");
//        \Log::info($request->all());
        
        
        $add_event = new Event;
//        if (isset($request['event_id'])) {
//            $add_event = Event::find($request['event_id']);
//            EventFollower::where('event_id', $add_event->id)->delete();
//            EventAttachment::where('event_id', $add_event->id)->delete();
//            EventIntrest::where('event_id', $add_event->id)->delete();
//        }
        $timestamp = strtotime($request['event_date']);
        $lat = $request['lat'];
        $lng = $request['lng'];
        $curl_url = "https://maps.googleapis.com/maps/api/timezone/json?location=$lat,$lng&timestamp=$timestamp&key=AIzaSyDdxlXEZmkr-7RJsFN7wqX5bJpBUTfzhxk";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        //$timezone = $response->timeZoneId;
        $sign = "-";
        $GMT_hours = "+00";
        $gmtTime = (abs($response->rawOffset) - $response->dstOffset);
        if ($response->status == 'OK') {
            if (strpos($response->rawOffset, '-') !== FALSE){
                $sign = "+";
            }
            
            $GMT_hours =   ($gmtTime / 60);
        }
        $event_date = $request['event_date'];
        $event_end_date = $request['event_end_date'];
        if($event_date >=  $event_end_date){
            $event_end_date=date("Y-m-d H:i:s", strtotime($event_end_date . " + 1 days"));
        }
        $timezone = ($GMT_hours);
        $utc_event_time = date("Y-m-d H:i:s", strtotime($event_date . " $timezone minutes"));
        $utc_event_end_time = date("Y-m-d H:i:s", strtotime($event_end_date . " $timezone minutes"));
        $add_event->user_id = $this->userId;
        $add_event->title = $request['title'];
        $add_event->description = utf8_decode($request['description']);
        $add_event->location = $request['location'];
        $add_event->phone_no = $request['phone_no'];
        $add_event->website_url = $request['website_url'];
        $add_event->lat = $request['lat'];
        $add_event->lng = $request['lng'];
        $add_event->event_date = $request['event_date'];
        $add_event->event_end_date = $request['event_end_date'];
        $add_event->utc_event_end_date = $utc_event_end_time;
        $add_event->utc_event_time = $utc_event_time;
        $add_event->is_private = $request['is_private'];
        $add_event->timezone = -($timezone / 60);
        
        $cover = Input::file('cover');
        if ($cover) {
            $type = $cover->getClientMimeType();
            if ($type == 'image/jpg' || $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/bmp') {
                $destination_path = 'public/images/events'; // upload path
                $extension = $cover->getClientOriginalExtension(); // getting image extension
                $coverfileName = 'eventimage_' . Str::random(15) . '.' . $extension; // renameing image
                $cover->move($destination_path, $coverfileName);
                $add_event->cover = asset('/public/images/events/'.$coverfileName);
            }
        }
        $add_event->ratio = $request['cover_ratio'];
        if($request['is_reoccuring']){
            $add_event->reoccure = $request['reoccure'];
            $add_event->reoccure_type = $request['reoccure_type'];
            $add_event->is_reoccuring_forever = $request['is_reoccuring_forever'];
            $add_event->reoccure_end_date = $request['reoccure_end_date'];
            $add_event->utc_reoccure_end_date = date("Y-m-d H:i:s", strtotime($request['utc_reoccure_end_date'] . " $timezone minutes"));
        }
        $add_event->is_reoccuring = $request['is_reoccuring'];
        
        $add_event->save();
        
        if($request['is_reoccuring']){ //save reoccurance
            $reoccurance = new EventReoccurance;
            $reoccurance->user_id = $this->userId;
            $reoccurance->event_id = $add_event->id;
            $reoccurance->reoccurance = $request['reoccurance'];
            $reoccurance->save();
        }

        if ($request['invited_ids']) {
            $invites_ids = explode(',', $request['invited_ids']);
            foreach ($invites_ids as $followers) {
                $check_event_followers = EventFollower::where(array('event_id' => $add_event->id, 'user_id' => $followers))->first();
                if (!$check_event_followers) {
                    $add_followrs = new EventFollower;
                    $add_followrs->event_id = $add_event->id;
                    $add_followrs->user_id = $followers;
                    $add_followrs->save();
                    $messagex = "You're invited to ".$request['title']." event";
                    //Save notification data
                    $notification = new Notification;
                    $notification->user_id = $this->userId;
                    $notification->on_user = $followers;
                    $notification->event_id = $add_event->id; 
                    $notification->notification_text = $messagex;
                    $notification->activity_text = 'You invited a user in event.';
                    $notification->type = 'Invite';
                    $notification->save();
                    $data['event_id'] = $add_event->id;
                    $data['event'] = $add_event;
                    //Send notification
//                    Log::info();
                    sendNotification($followers,$data,'', $messagex);
                }
            }
        }

        if ($request['interest_ids']) {
            $interests_ids = explode(',', $request['interest_ids']);
            foreach ($interests_ids as $interest_id) {
                $add_followrs = EventIntrest::where(array('event_id' => $add_event->id, 'interest_id' => $interest_id))->first();
                if (!$add_followrs) {
                    $add_followrs = new EventIntrest;
                    
                }
                $add_followrs->event_id = $add_event->id;
                $add_followrs->interest_id = $interest_id;
                $add_followrs->save();
            }
        }

        $files = Input::file('files');
        if ($files) {
            $index = 0;
            foreach ($files as $file) {
                if ($file->getClientOriginalExtension() != 'exe') {
                    $type = $file->getClientMimeType();
                    if ($type == 'image/jpg' || $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/bmp') {
                        $destination_path = 'public/images/events'; // upload path
                        $extension = $file->getClientOriginalExtension(); // getting image extension
                        $fileName = 'eventimage_' . Str::random(15) . '.' . $extension; // renameing image
                        $file->move($destination_path, $fileName);
                        $add_event_image = new EventAttachment;
                        $add_event_image->event_id = $add_event->id;
                        $add_event_image->attachment_path = $fileName;
                        $add_event_image->type = 'image';
                        $add_event_image->poster = '';
                        $add_event_image->image_ratio = $request['images_ratio'][$index];
                        $add_event_image->save();
                    }
                    //for videos
                    else {
                        $video_extension = $file->getClientOriginalExtension();
                        $type = $file->getClientMimeType();
                        $allowedextentions = ["mov", "MOV", "3g2", "3gp", "4xm", "a64", "aa", "aac", "ac3", "act", "adf", "adp", "adts", "adx", "aea", "afc", "aiff", "alaw", "alias_pix", "alsa", "amr", "anm", "apc", "ape", "apng",
                            "aqtitle", "asf", "asf_o", "asf_stream", "ass", "ast", "au", "avi", "avisynth", "avm2", "avr", "avs", "bethsoftvid", "bfi", "bfstm", "bin", "bink", "bit", "bmp_pipe",
                            "bmv", "boa", "brender_pix", "brstm", "c93", "caf", "cavsvideo", "cdg", "cdxl", "cine", "concat", "crc", "dash", "data", "daud", "dds_pipe", "dfa", "dirac", "dnxhd",
                            "dpx_pipe", "dsf", "dsicin", "dss", "dts", "dtshd", "dv", "dv1394", "dvbsub", "dvd", "dxa", "ea", "ea_cdata", "eac3", "epaf", "exr_pipe", "f32be", "f32le", "f4v",
                            "f64be", "f64le", "fbdev", "ffm", "ffmetadata", "film_cpk", "filmstrip", "flac", "flic", "flv", "framecrc", "framemd5", "frm", "g722", "g723_1", "g729", "gif", "gsm", "gxf",
                            "h261", "h263", "h264", "hds", "hevc", "hls", "hls", "applehttp", "hnm", "ico", "idcin", "idf", "iff", "ilbc", "image2", "image2pipe", "ingenient", "ipmovie",
                            "ipod", "ircam", "ismv", "iss", "iv8", "ivf", "j2k_pipe", "jacosub", "jpeg_pipe", "jpegls_pipe", "jv", "latm", "lavfi", "live_flv", "lmlm4", "loas", "lrc",
                            "lvf", "lxf", "m4v", "matroska", "mkv", "matroska", "webm", "md5", "mgsts", "microdvd", "mjpeg", "mkvtimestamp_v2", "mlp", "mlv", "mm", "mmf", "mp4", "m4a", "3gp",
                            "3g2", "mj2", "mp2", "mp3", "mp4", "mpc", "mpc8", "mpeg", "mpeg1video", "mpeg2video", "mpegts", "mpegtsraw", "mpegvideo", "mpjpeg", "mpl2", "mpsub", "msnwctcp",
                            "mtv", "mulaw", "mv", "mvi", "mxf", "mxf_d10", "mxf_opatom", "mxg", "nc", "nistsphere", "nsv", "null", "nut", "nuv", "oga", "ogg", "oma", "opus", "oss", "paf",
                            "pictor_pipe", "pjs", "pmp", "png_pipe", "psp", "psxstr", "pulse", "pva", "pvf", "qcp", "qdraw_pipe", "r3d", "rawvideo", "realtext", "redspark", "rl2", "rm",
                            "roq", "rpl", "rsd", "rso", "rtp", "rtp_mpegts", "rtsp", "s16be", "s16le", "s24be", "s24le", "s32be", "s32le", "s8", "sami", "sap", "sbg", "sdl", "sdp", "sdr2",
                            "segment", "sgi_pipe", "shn", "siff", "singlejpeg", "sln", "smjpeg", "smk", "smoothstreaming", "smush", "sol", "sox", "spdif", "spx", "srt", "stl",
                            "stream_segment", "ssegment", "subviewer", "subviewer1", "sunrast_pipe", "sup", "svcd", "swf", "tak", "tedcaptions", "tee", "thp", "tiertexseq",
                            "tiff_pipe", "tmv", "truehd", "tta", "tty", "txd", "u16be", "u16le", "u24be", "u24le", "u32be", "u32le", "u8", "uncodedframecrc", "v4l2", "vc1", "vc1test",
                            "vcd", "video4linux2", "v4l2", "vivo", "vmd", "vob", "vobsub", "voc", "vplayer", "vqf", "w64", "wav", "wc3movie", "webm", "webm_chunk", "webm_dash_manife",
                            "webp", "webp_pipe", "webvtt", "wsaud", "wsvqa", "wtv", "wv", "x11grab", "xa", "xbin", "xmv", "xv", "xwma", "wmv", "yop", "yuv4mpegpipe"];
                        if (in_array($video_extension, $allowedextentions)) {
                            $video_destinationPath = base_path('public/videos/events'); // upload path
                            $video_fileName = 'video_' . Str::random(15) . '.' . 'mp4'; // renameing image

                            $fileDestination = $video_destinationPath . '/' . $video_fileName;
                            $filePath = $file->getRealPath();
                            exec("ffmpeg -i $filePath -strict -2 $fileDestination 2>&1", $result, $status);
                            $bytes = $file->getClientSize();
                            if ($status === 0) {
                                $info = $this->getVideoInformation($result);
                                $poster_name = explode('.', $video_fileName)[0] . '.jpg';
                                $poster = 'public/images/posters/' . $poster_name;
                                exec("ffmpeg -ss $info[1] -i $filePath -frames:v 1 $poster 2>&1");
                                $sizes = getimagesize(asset('public/images/posters/' . $poster_name));
                                $image_ratio = $sizes[0] / $sizes[1];
                                $bytes = File::size($video_destinationPath . '/' . $video_fileName);
                            } else {
                                $poster_name = '';
                                $image_ratio = '';
                            }
                            $add_event_video = new EventAttachment;
                            $add_event_video->event_id = $add_event->id;
                            $add_event_video->attachment_path = $video_fileName;
                            $add_event_video->type = 'video';
                            $add_event_video->poster = $poster_name;
                            $add_event_video->image_ratio = $image_ratio;
                            $add_event_video->save();
                        }
                    }
                }
                $index++;
            }
        }

        $message = 'Event Added SuccessFully';
        if (isset($request['event_id'])) {
            $message = 'Event Updated SuccessFully';
        }
        $event = Event::selectRaw("*,( 6371 * acos( cos( radians($this->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($this->lng) ) + sin( radians($this->lat) ) * sin( radians(lat) ) ) ) AS distance")
                ->with('user', 'likes', 'likes.user', 'comments', 'comments.user', 'attachments', 'interests', 'interests.interest','Invites.user', 'Reoccurance')
                ->orderBy('utc_event_time', 'desc')
                ->where('id', $add_event->id)
                ->withCount('likes', 'arrived', 'userArrived', 'userLiked')->first();
        return sendSuccess($message, $event);
    }

    private function getVideoInformation($video_information) {
        $regex_duration = "/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/";
        if (preg_match($regex_duration, implode(" ", $video_information), $regs)) {
            $hours = $regs [1] ? $regs [1] : null;
            $mins = $regs [2] ? $regs [2] : null;
            $secs = $regs [3] ? $regs [3] : null;
            $ms = $regs [4] ? $regs [4] : null;
            $random_duration = sprintf("%02d:%02d:%02d", rand(0, $hours), rand(0, $mins), rand(0, $secs));
            $original_duration = $hours . ":" . $mins . ":" . $secs;
            $parsed = date_parse($original_duration);
            $seconds = ($parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second']) > 20 ? true : false;
            return [$original_duration, $random_duration, $seconds];
        }
    }

    function deleteEvent($event_id) {
        $event = Event::find($event_id);
        if ($event) {
            if ($event->user_id == $this->userId) {
                $event->delete();
                return sendSuccess("Event Deleted Successfully", "");
            } else {
                return sendError("You Are Not Autherize to delete this event", 411);
            }
        } else {
            return sendError("Event Not Found", 411);
        }
    }

    function getMyEvents() {
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $data['events'] = Event::selectRaw("*,( 6371 * acos( cos( radians($this->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($this->lng) ) + sin( radians($this->lat) ) * sin( radians(lat) ) ) ) AS distance")
                ->where('user_id', $this->userId)
                ->with('user', 'likes.user', 'comments.user', 'Invites.user', 'attachments', 'interests', 'interests.interest', 'arrived.user')
                ->with(['comments' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['likes' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['Invites' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['arrived' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])
                ->orderBy('event_date', 'asc')->orderBy('distance', 'asc')
                ->withCount('likes', 'arrived', 'userArrived', 'userLiked')
                ->get();
        return sendSuccess('', $data);
    }
    
    
    function getMyInvitedEvents() {
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $data['events'] = Event::selectRaw("*,( 6371 * acos( cos( radians($this->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($this->lng) ) + sin( radians($this->lat) ) * sin( radians(lat) ) ) ) AS distance")
//                ->where('user_id', $this->userId)
                ->with('user', 'likes.user', 'comments.user', 'Invites.user', 'attachments', 'interests', 'interests.interest', 'arrived.user')
                ->with(['comments' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['likes' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['Invites' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])->with(['arrived' => function($query)use ($blocked_ids) {
                        $query->whereNotIn('user_id', $blocked_ids);
                    }])
//                    ->where('utc_event_end_date', '>', Carbon::now())
                ->orderBy('utc_event_time', 'desc')->orderBy('distance', 'asc')
                ->withCount('likes', 'arrived', 'userArrived', 'userLiked')
                ->whereHas('userInvites')
                ->get();
        return sendSuccess('', $data);
    }
    
    
    function getEventsByInterest($interest_id) {
        $events = EventIntrest::where('interest_id', $interest_id)->pluck('event_id')->toArray();
        
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        $data['events'] = Event::selectRaw("*,( 6371 * acos( cos( radians($this->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($this->lng) ) + sin( radians($this->lat) ) * sin( radians(lat) ) ) ) AS distance")
                ->whereIn('id', $events)
                ->with('user', 'likes.user', 'comments.user', 'Invites.user', 'attachments', 'interests', 'interests.interest', 'arrived.user')
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
                ->orderBy('utc_event_time', 'asc')->orderBy('distance', 'asc')
                ->withCount('likes', 'arrived', 'userArrived', 'userLiked')
                ->get();
        return sendSuccess('', $data);
    }
    

    function editEvent(Request $request) {
        
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $validation = $this->validate($request, [
            'event_date' => 'required',
            'title' => 'required',
            'location' => 'required',
//            'phone_no' => 'required',
//            'website_url' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'is_private' => 'required',
            'event_id' => 'required'
        ]);
        if (!$validation) {
            return Response::json(array('status' => 'error', 'errorMessage' => $validation));
        }
        $add_event = Event::find($request['event_id']);
//        EventFollower::where('event_id', $add_event->id)->delete();
//        EventIntrest::where('event_id', $add_event->id)->delete();

        $timestamp = strtotime($request['event_date']);
        $lat = $request['lat'];
        $lng = $request['lng'];
        $curl_url = "https://maps.googleapis.com/maps/api/timezone/json?location=$lat,$lng&timestamp=$timestamp&key=AIzaSyDdxlXEZmkr-7RJsFN7wqX5bJpBUTfzhxk";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        //$timezone = $response->timeZoneId;
        $sign = "-";
        $GMT_hours = "+00";
        $gmtTime = (abs($response->rawOffset) - $response->dstOffset);
        if ($response->status == 'OK') {
            if (strpos($response->rawOffset, '-') !== FALSE){
                $sign = "+";
            }
            
            $GMT_hours =   ($gmtTime / 60);
        }
        $event_date = $request['event_date'];
        $event_end_date = $request['event_end_date'];
        $timezone = $GMT_hours;
        $utc_event_time = date("Y-m-d H:i:s", strtotime($event_date . " $timezone minutes"));
        $utc_event_end_time = date("Y-m-d H:i:s", strtotime($event_end_date . " $timezone minutes"));
        $add_event->user_id = $this->userId;
        $add_event->title = $request['title'];
        $add_event->description = utf8_decode($request['description']);
        $add_event->location = $request['location'];
        $add_event->phone_no = $request['phone_no'];
        $add_event->website_url = $request['website_url'];
        $add_event->lat = $request['lat'];
        $add_event->lng = $request['lng'];
        $add_event->event_date = $request['event_date'];
        $add_event->event_end_date = $request['event_end_date'];
        $add_event->utc_event_end_date = $utc_event_end_time;
        $add_event->utc_event_time = $utc_event_time;
        $add_event->is_private = $request['is_private'];
        $add_event->timezone = -($timezone / 60);
        $cover = Input::file('cover');
        if ($cover) {
            $type = $cover->getClientMimeType();
            if ($type == 'image/jpg' || $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/bmp') {
                $destination_path = 'public/images/events'; // upload path
                $extension = $cover->getClientOriginalExtension(); // getting image extension
                $coverfileName = 'eventimage_' . Str::random(15) . '.' . $extension; // renameing image
                $cover->move($destination_path, $coverfileName);
                $add_event->cover = asset('/public/images/events/'.$coverfileName);
            }
        }
        if($request['is_reoccuring']){
            $add_event->reoccure = $request['reoccure'];
            $add_event->reoccure_type = $request['reoccure_type'];
            $add_event->is_reoccuring_forever = $request['is_reoccuring_forever'];
            $add_event->reoccure_end_date = $request['reoccure_end_date'];
            $add_event->utc_reoccure_end_date = date("Y-m-d H:i:s", strtotime($request['utc_reoccure_end_date'] . " $timezone minutes"));
        }
        $add_event->is_reoccuring = $request['is_reoccuring'];
        
        $add_event->ratio = $request['cover_ratio'];
        $add_event->save();
        
        if($request['is_reoccuring']){ //update reoccurance
            EventReoccurance::where(['event_id'=>$add_event->id])->update(['reoccurance'=>$request['reoccurance']]);
            
        }

        if ($request['invited_ids']) {
            $invites_ids = explode(',', $request['invited_ids']);
            foreach ($invites_ids as $followers) {
                $add_followrs = EventFollower::where(array('event_id' => $add_event->id, 'user_id' => $followers))->first();
                if (!$add_followrs) {
                    $add_followrs = new EventFollower;
                }
                $add_followrs->event_id = $add_event->id;
                $add_followrs->user_id = $followers;
                $add_followrs->save();
                
            }
        }

        if ($request['interest_ids']) {
            $interests_ids = explode(',', $request['interest_ids']);
            foreach ($interests_ids as $interest_id) {
                $add_followrs = EventIntrest::where(array('event_id' => $add_event->id, 'interest_id' => $interest_id))->first();
                if (!$add_followrs) {
                    $add_followrs = new EventIntrest;
                }
                $add_followrs->event_id = $add_event->id;
                $add_followrs->interest_id = $interest_id;
                $add_followrs->save();
            }
        }

        if ($request['delete_attachment']) {
            $delete_id = explode(',', $request['delete_attachment']);
            foreach ($delete_id as $delete) {
                EventAttachment::where('id', $delete)->delete();
            }
        }
        $files = Input::file('files');
        if ($files) {
            $index = 0;
            foreach ($files as $file) {
                if ($file->getClientOriginalExtension() != 'exe') {
                    $type = $file->getClientMimeType();
                    if ($type == 'image/jpg' || $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/bmp') {
                        $destination_path = 'public/images/events'; // upload path
                        $extension = $file->getClientOriginalExtension(); // getting image extension
                        $fileName = 'eventimage_' . Str::random(15) . '.' . $extension; // renameing image
                        $file->move($destination_path, $fileName);
                        $add_event_image = new EventAttachment;
                        $add_event_image->event_id = $add_event->id;
                        $add_event_image->attachment_path = $fileName;
                        $add_event_image->type = 'image';
                        $add_event_image->poster = '';
                        $add_event_image->image_ratio = $request['images_ratio'][$index];
                        $add_event_image->save();
                    }
                    //for videos
                    else {
                        $video_extension = $file->getClientOriginalExtension();
                        $type = $file->getClientMimeType();
                        $allowedextentions = ["mov", "MOV", "3g2", "3gp", "4xm", "a64", "aa", "aac", "ac3", "act", "adf", "adp", "adts", "adx", "aea", "afc", "aiff", "alaw", "alias_pix", "alsa", "amr", "anm", "apc", "ape", "apng",
                            "aqtitle", "asf", "asf_o", "asf_stream", "ass", "ast", "au", "avi", "avisynth", "avm2", "avr", "avs", "bethsoftvid", "bfi", "bfstm", "bin", "bink", "bit", "bmp_pipe",
                            "bmv", "boa", "brender_pix", "brstm", "c93", "caf", "cavsvideo", "cdg", "cdxl", "cine", "concat", "crc", "dash", "data", "daud", "dds_pipe", "dfa", "dirac", "dnxhd",
                            "dpx_pipe", "dsf", "dsicin", "dss", "dts", "dtshd", "dv", "dv1394", "dvbsub", "dvd", "dxa", "ea", "ea_cdata", "eac3", "epaf", "exr_pipe", "f32be", "f32le", "f4v",
                            "f64be", "f64le", "fbdev", "ffm", "ffmetadata", "film_cpk", "filmstrip", "flac", "flic", "flv", "framecrc", "framemd5", "frm", "g722", "g723_1", "g729", "gif", "gsm", "gxf",
                            "h261", "h263", "h264", "hds", "hevc", "hls", "hls", "applehttp", "hnm", "ico", "idcin", "idf", "iff", "ilbc", "image2", "image2pipe", "ingenient", "ipmovie",
                            "ipod", "ircam", "ismv", "iss", "iv8", "ivf", "j2k_pipe", "jacosub", "jpeg_pipe", "jpegls_pipe", "jv", "latm", "lavfi", "live_flv", "lmlm4", "loas", "lrc",
                            "lvf", "lxf", "m4v", "matroska", "mkv", "matroska", "webm", "md5", "mgsts", "microdvd", "mjpeg", "mkvtimestamp_v2", "mlp", "mlv", "mm", "mmf", "mp4", "m4a", "3gp",
                            "3g2", "mj2", "mp2", "mp3", "mp4", "mpc", "mpc8", "mpeg", "mpeg1video", "mpeg2video", "mpegts", "mpegtsraw", "mpegvideo", "mpjpeg", "mpl2", "mpsub", "msnwctcp",
                            "mtv", "mulaw", "mv", "mvi", "mxf", "mxf_d10", "mxf_opatom", "mxg", "nc", "nistsphere", "nsv", "null", "nut", "nuv", "oga", "ogg", "oma", "opus", "oss", "paf",
                            "pictor_pipe", "pjs", "pmp", "png_pipe", "psp", "psxstr", "pulse", "pva", "pvf", "qcp", "qdraw_pipe", "r3d", "rawvideo", "realtext", "redspark", "rl2", "rm",
                            "roq", "rpl", "rsd", "rso", "rtp", "rtp_mpegts", "rtsp", "s16be", "s16le", "s24be", "s24le", "s32be", "s32le", "s8", "sami", "sap", "sbg", "sdl", "sdp", "sdr2",
                            "segment", "sgi_pipe", "shn", "siff", "singlejpeg", "sln", "smjpeg", "smk", "smoothstreaming", "smush", "sol", "sox", "spdif", "spx", "srt", "stl",
                            "stream_segment", "ssegment", "subviewer", "subviewer1", "sunrast_pipe", "sup", "svcd", "swf", "tak", "tedcaptions", "tee", "thp", "tiertexseq",
                            "tiff_pipe", "tmv", "truehd", "tta", "tty", "txd", "u16be", "u16le", "u24be", "u24le", "u32be", "u32le", "u8", "uncodedframecrc", "v4l2", "vc1", "vc1test",
                            "vcd", "video4linux2", "v4l2", "vivo", "vmd", "vob", "vobsub", "voc", "vplayer", "vqf", "w64", "wav", "wc3movie", "webm", "webm_chunk", "webm_dash_manife",
                            "webp", "webp_pipe", "webvtt", "wsaud", "wsvqa", "wtv", "wv", "x11grab", "xa", "xbin", "xmv", "xv", "xwma", "wmv", "yop", "yuv4mpegpipe"];
                        if (in_array($video_extension, $allowedextentions)) {
                            $video_destinationPath = base_path('public/videos/events'); // upload path
                            $video_fileName = 'video_' . Str::random(15) . '.' . 'mp4'; // renameing image

                            $fileDestination = $video_destinationPath . '/' . $video_fileName;
                            $filePath = $file->getRealPath();
                            exec("ffmpeg -i $filePath -strict -2 $fileDestination 2>&1", $result, $status);
                            $bytes = $file->getClientSize();
                            if ($status === 0) {
                                $info = $this->getVideoInformation($result);
                                $poster_name = explode('.', $video_fileName)[0] . '.jpg';
                                $poster = 'public/images/posters/' . $poster_name;
                                exec("ffmpeg -ss $info[1] -i $filePath -frames:v 1 $poster 2>&1");
                                $sizes = getimagesize(asset('public/images/posters/' . $poster_name));
                                $image_ratio = $sizes[0] / $sizes[1];
                                $bytes = File::size($video_destinationPath . '/' . $video_fileName);
                            } else {
                                $poster_name = '';
                                $image_ratio = '';
                            }
                            $add_event_video = new EventAttachment;
                            $add_event_video->event_id = $add_event->id;
                            $add_event_video->attachment_path = $video_fileName;
                            $add_event_video->type = 'video';
                            $add_event_video->poster = $poster_name;
                            $add_event_video->image_ratio = $image_ratio;
                            $add_event_video->save();
                        }
                    }
                }
                $index++;
            }
        }

        $message = 'Event Added SuccessFully';
        if (isset($request['event_id'])) {
            $message = 'Event Updated SuccessFully';
        }
        $event = Event::selectRaw("*,( 6371 * acos( cos( radians($this->lat))*cos( radians(lat) ) * cos( radians(lng) - radians($this->lng) ) + sin( radians($this->lat) ) * sin( radians(lat) ) ) ) AS distance")
                        ->with('user', 'likes', 'likes.user', 'comments', 'comments.user', 'attachments', 'interests', 'interests.interest','Invites.user', 'Reoccurance')
                        ->orderBy('utc_event_time', 'desc')
                        ->where('id', $add_event->id)
                        ->withCount('likes', 'arrived', 'userArrived', 'userLiked')->first();
        return sendSuccess($message, $event);
    }

}

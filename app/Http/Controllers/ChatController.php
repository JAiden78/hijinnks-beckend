<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
//Models
use App\Chat;
use App\ChatMessage;
use App\BlockUser;
use App\Notification;

class ChatController extends Controller {

    private $userId;
    private $userName;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            $this->userName = Auth::user()->username;
            return $next($request);
        });
    }

    function sendMessage(Request $request) {
        $validation = $this->validate($request, [
            'receiver_id' => 'required',
            'message' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }
        $sender_id = $this->userId;
        $receiver_id = $request['receiver_id'];
        $chat_user = Chat::where('sender_id', $sender_id)
                ->where('receiver_id', $receiver_id)
                ->orwhere(function($q) use($receiver_id, $sender_id) {
                    $q->where('sender_id', $receiver_id);
                    $q->Where('receiver_id', $sender_id);
                })
                ->first();
        if ($chat_user) {
            if ($chat_user->receiver_id == $sender_id) {
                $chat_user->receiver_deleted = 0;
                $chat_user->save();
            }
            if ($chat_user->sender_id == $sender_id) {
                $chat_user->sender_deleted = 0;
                $chat_user->save();
            }
        }
        if (!$chat_user) {
            $chat_user = new Chat;
            $chat_user->sender_id = $sender_id;
            $chat_user->receiver_id = $receiver_id;
            $chat_user->save();
        }
        $message = new ChatMessage;
        $message->sender_id = $sender_id;
        $message->receiver_id = $receiver_id;
        $message->chat_id = $chat_user->id;
        if ($request['message']) {
            $message->message = $request['message'];
        }
        $message->save();
        Chat::where('sender_id', $sender_id)
                ->Where('receiver_id', $receiver_id)
                ->orwhere('sender_id', $receiver_id)
                ->Where('receiver_id', $sender_id)
                ->update(['last_message_id' => $message->id]);
        $messagex = $this->userName. ' sent you a private message.';
        $datatosend=ChatMessage::where('id',$message->id)->with('sender', 'receiver')->first();
        $data['message'] = $datatosend;
        
        //Save notification data
        $notification = new Notification;
        $notification->user_id = $sender_id;
        $notification->on_user = $receiver_id;
        $notification->notification_text = $messagex;
        $notification->activity_text = 'You send a private message.';
        $notification->type = 'Message';
        $notification->save();
        //Send notification
        sendNotification($receiver_id,$data,'', $messagex);
        
        return sendSuccess('Message sent successfully.', $datatosend);
    }

    function readMessages(Request $request) {
        $validation = $this->validate($request, [
            'chat_id' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }

        $receiver_id = $this->userId;

        $messages = ChatMessage::where(['chat_id' => $request['chat_id'], 'receiver_id' => $receiver_id])->update(['is_read' => 1]);
        return Response::json(array('status' => 'success', 'successData' => $messages, 'successMessage' => 'Messages read successfully.'));
    }

    function deleteMessage(Request $request) {
        $validation = $this->validate($request, [
            'message_id' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }

        $user_id = $this->userId;

        $message = ChatMessage::find($request['message_id']);
        if ($message->sender_id == $user_id) {
            $message->sender_deleted = 1;
        } elseif ($message->receiver_id == $user_id) {
            $message->receiver_deleted = 1;
        }
        $message->is_read = 1;
        $message->save();
        return sendSuccess('Messages deleted successfully.', $message);
//        return Response::json(array('status' => 'success', 'successData' => $message, 'successMessage' => 'Messages deleted successfully.'));
    }
function editMessage(Request $request) {
        $validation = $this->validate($request, [
            'message_id' => 'required',
            'message' => 'required',
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }
        $message =  ChatMessage::find($request->message_id);
        $message->message = $request['message'];
        $message->save();
        return sendSuccess('Messages Updated successfully.', $message);
//        return Response::json(array('status' => 'success', 'successData' => $message, 'successMessage' => 'Messages deleted successfully.'));
    }
    function deleteChat(Request $request) {
        $validation = $this->validate($request, [
            'chat_id' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }

        $user_id = $this->userId;

        $chat = Chat::find($request['chat_id']);
        if ($chat->sender_id == $user_id) {
            $chat->sender_deleted = 1;
        } elseif ($chat->receiver_id == $user_id) {
            $chat->receiver_deleted = 1;
        }
        $chat->save();

        ChatMessage::where(['chat_id' => $request['chat_id'], 'receiver_id' => $user_id])->update(['receiver_deleted' => 1, 'is_read' => 1]);
        ChatMessage::where(['chat_id' => $request['chat_id'], 'sender_id' => $user_id])->update(['sender_deleted' => 1, 'is_read' => 1]);

        return Response::json(array('status' => 'success', 'successData' => '', 'successMessage' => 'Chat Deleted successfully.'));
    }

    function getDetailChat(Request $request) {
        $validation = $this->validate($request, [
            'user_id' => 'required'
        ]);
        if (!$validation) {
            return sendError($validation, 400);
        }
        $other_user = $request['user_id'];
        $user_id = $this->userId;
        ChatMessage::where(array('sender_id' => $other_user, 'receiver_id' => $user_id))->update(['is_read' => 1]);
        $messages = ChatMessage::with('sender', 'receiver')
                ->where(function ($q) use($user_id) {
                    $q->where('sender_id', $user_id);
                    $q->orWhere('receiver_id', $user_id);
                })
                ->where(function ($q) use($other_user) {
                    $q->where('sender_id', $other_user);
                    $q->orWhere('receiver_id', $other_user);
                })
                ->whereRaw("IF(`sender_id` = $user_id, `sender_deleted`, `receiver_deleted`)= 0")
                ->get();
        return sendSuccess('', $messages);
    }

    function getChats() {
        Notification::where('on_user', $this->userId)->where('type','Message')->update(['is_read' => 1]);
        $user_id = $this->userId;
        $blocked_ids = BlockUser::select('user_id')->where(array('blocked_id' => $this->userId))->get()->toArray();
        
        $chats = Chat::with('sender', 'receiver')
                ->withCount(['messages' => function ($q) use($user_id){
                        $q->where('is_read', 0)->whereRaw("IF(`sender_id` = $user_id, `sender_deleted`, `receiver_deleted`)= 0");
                    }])->withCount(['allMessages' => function ($q) use($user_id){
                        $q->whereRaw("IF(`sender_id` = $user_id, `sender_deleted`, `receiver_deleted`)= 0");
                    }])
                    
                ->where(function ($q) use($user_id,$blocked_ids) {
                    $q->where('sender_id', $user_id);
                    $q->orWhere('receiver_id', $user_id);
                })->whereNotIn('sender_id', $blocked_ids)
                   ->WhereNotIn('receiver_id', $blocked_ids)
                
                ->whereRaw("IF(`sender_id` = $user_id, `sender_deleted`, `receiver_deleted`)= 0")
                ->orderBy('updated_at', 'desc')
                ->get();
        return sendSuccess('', $chats);
    }

}

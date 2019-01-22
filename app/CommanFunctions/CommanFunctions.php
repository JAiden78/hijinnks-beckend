<?php

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use App\User;
use Illuminate\Support\Facades\Log;
use App\Notification;

function sendSuccess($message, $data) {
    return Response::json(array('status' => 'success', 'successMessage' => $message, 'successData' => $data), 200, []);
}

function sendError($error_message, $code) {
    return Response::json(array('status' => 'error', 'errorMessage' => $error_message), $code);
}

function addFile($file, $path) {
    if ($file) {
        if ($file->getClientOriginalExtension() != 'exe') {
            $type = $file->getClientMimeType();
            if ($type == 'image/jpg' || $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/bmp') {
                $destination_path = 'public/images/' . $path; // upload path
                $extension = $file->getClientOriginalExtension(); // getting image extension
                $fileName = 'image_' . Str::random(15) . '.' . $extension; // renameing image
                $file->move($destination_path, $fileName);
                $file_path = $fileName;
                return $file_path;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function sendNotification($userId, $data, $url = '', $message) {
    $user = User::find($userId);
//Log::info($userId);
    if ($user->push_notification) {
        $ios_badgeType = 'SetTo';
        $ios_badgeCount = Notification::where('on_user', $userId)->where('is_read', 0)->count();
        OneSignal::sendNotificationUsingTags(
                $message, [array("key" => "user_id", "relation" => "=", "value" => $userId)], $url, $data, $buttons = null, $schedule = null,$ios_badgeType,$ios_badgeCount
        );
    }
}

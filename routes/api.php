<?php

use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

Route::get('test_nitification', function () {
    $heading = 'Test Notification';
    $message = 'I am Testing';
    $data['message'] = 'Test Message';
    $user_id = 138;
    sendNotification($user_id, $data, $url= '', $message);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1', 'middleware' => ['headersmid', 'checkAppKey']], function () {
    Route::post('/register', 'AuthController@postRegister');
    Route::post('/login', 'AuthController@postLogin');
    Route::post('/fblogin', 'AuthController@fbLogin');
    Route::post('/twitter_login', 'AuthController@twitterLogin');
    Route::post('/forgetpassword', 'AuthController@forgetPasswordMail');


    Route::group(['middleware' => ['checkSession']], function () {
        Route::get('/get_dashboard', 'DashboardController@index');
        Route::get('/dashborad_events', 'DashboardController@mainEvents');
        Route::get('/get_rsvp/{user_id}', 'DashboardController@getRsvpEvent');
        Route::get('/get_event/{event_id}', 'DashboardController@getEvent');
        Route::get('/get_fav', 'DashboardController@getFavrities');
        Route::get('/increment_phone_view_count/{event_id}', 'DashboardController@incrementPhoneViewCount');
        Route::get('/increment_website_view_count/{event_id}', 'DashboardController@incrementWebsiteViewCount');
        Route::post('/add_event', 'EventsController@addEvent');
        Route::post('/edit_event', 'EventsController@editEvent');
        Route::post('/add_like', 'ActionController@addLike');
        Route::post('/add_share', 'ActionController@addShare');
        Route::post('/remove_like', 'ActionController@addUnLike');
        Route::post('/add_comment', 'ActionController@addComment');
        Route::get('/search', 'ActionController@searchUser');
        Route::get('/main_search', 'ActionController@search');
        Route::get('/get_all_users', 'ActionController@getAllUser');
        Route::post('/add_rsvp', 'ActionController@addRsvp');
        Route::get('/get_user/{id}', 'UserController@getUser');
        Route::post('/add_follow', 'FollowManagmnetController@addFollower');
        Route::get('/get_follower/{user_id}', 'FollowManagmnetController@getFollowers');
        Route::post('/un_follow', 'FollowManagmnetController@unFollow');
        Route::get('/get_following/{user_id}', 'FollowManagmnetController@getFollowing');
        Route::get('/get_all_intrests', 'UserController@getAllIntrests');
        Route::get('/get_all_user_intrests', 'UserController@getAllUserIntrests');
        Route::get('/get_intrest/{user_id}', 'UserController@getIntrest');
        Route::post('/update_profile', 'UserController@changeProfile');
        Route::post('/block_user', 'UserController@blockUnBlockUser');
        Route::get('/get_blocked', 'UserController@getBlocked');
        Route::get('/block_user', 'UserController@getBlockUser');
        Route::get('/user_block_user', 'UserController@getUserBlocked');
        Route::post('/update_private', 'UserController@updatePrivate');
        Route::post('/update_notification', 'UserController@updateNotification');
        Route::post('/update_cover', 'UserController@changeCover');
        Route::post('/update_profile_image', 'UserController@changeProfileImage');
        Route::get('/get_notifications', 'UserController@getNotifications');
        Route::post('/add_intrest', 'UserController@addIntrest');
        Route::post('/add_gender', 'UserController@addGender');
        Route::get('/delete_intrest/{user_id}', 'UserController@deleteIntrest');
        Route::get('/get_counts', 'UserController@getCount');
        Route::get('/get_my_events', 'EventsController@getMyEvents');
        Route::get('/get_my_invited_events', 'EventsController@getMyInvitedEvents');
        Route::get('/get_events_by_interest/{interest_id}', 'EventsController@getEventsByInterest');
        Route::get('/delete_event/{event_id}', 'EventsController@deleteEvent');
                //      Chat Section
        Route::post('send_message/', 'ChatController@sendMessage');
        Route::post('read_messages/', 'ChatController@readMessages');
        Route::post('delete_message/', 'ChatController@deleteMessage');
        Route::post('delete_chat/', 'ChatController@deleteChat');
        Route::post('edit_message/', 'ChatController@editMessage');
        Route::post('get_detail_chat/', 'ChatController@getDetailChat');
        Route::get('get_chats/', 'ChatController@getChats');
//        Support Section sendMail
        Route::post('/send_mail', 'UserController@sendMail');
    });
    
});

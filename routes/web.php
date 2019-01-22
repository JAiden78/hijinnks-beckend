<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    echo 'Working . . . . . .';
//    return view('welcome');
});
Route::get('cron','AuthController@reOccurEvent');
Route::get('/adminlogin', function () {
    if(Auth::user()){
       return redirect('dashboard'); 
    }else{
    $data['title']='Admin Login';
    return view('adminlogin',$data);
    }});

Route::post('adminlogin','AuthController@adminLogin');

Route::get('userlogout', function() {
   
    Auth::logout();
    return Redirect::to(\Illuminate\Support\Facades\URL::previous());
});
Route::group(['middleware' => ['nocache', 'auth']], function () {
    Route::get('dashboard','AdminController@dashboard');
    Route::get('users','AdminController@users');
    Route::get('get_users_by_gender/{gender}','AdminController@getUserByGender');
    Route::get('get_users_by_login_type/{type}','AdminController@getUserByLoginType');
    Route::get('delete_user/{user_id}','AdminController@deleteUser');
    Route::get('ban_user/{user_id}','AdminController@banUser');
    Route::get('unban_user/{user_id}','AdminController@removeBanUser');

    Route::get('events','AdminController@events');
    Route::get('hijinnks-events','AdminController@hijinnks_events');
    
    Route::post('events_datatable_pagination','AdminController@eventsDataTablePagination');
    
    Route::post('get_events','AdminController@getEvents');

    // Route::get('delete_event/{user_id}','AdminController@deleteEvent');//
    Route::get('delete_event/{user_id}','AdminController@deleteEventAdmin');
    
    Route::get('change_password','AdminController@changePassword');
    Route::post('change_password','AdminController@updatePassword');
    Route::post('change_name','AdminController@updateName');

    Route::get('get_user_events/{user_id}','AdminController@getUserEvents');
    Route::post('get_user_events','AdminController@getUserEventsPost');

//
//    Route::get('get_user_old_events/{user_id}','AdminController@getUserOldEvents');
//    Route::get('get_user_ongoing_events/{user_id}','AdminController@getUserOngoingEvents');
//    Route::get('get_user_upcoming_events/{user_id}','AdminController@getUserUpcomingEvents');

    Route::get('event_attachments/{event_id}','AdminController@eventAttachments');
    Route::get('event_details/{event_id}','AdminController@eventDetails');
    Route::get('user_event_details/{event_id}','AdminController@userEventDetails');
    Route::get('interests','AdminController@interests');
    Route::get('events_by_interest/{interest_id}','AdminController@eventsByInterest');
    Route::get('users_by_interest/{interest_id}','AdminController@usersByInterest');
    Route::post('interests','AdminController@addInterests');
    Route::get('delete_interest/{interest_id}','AdminController@deleteInterest');

    Route::get('follower/{event_id}','AdminController@getFollowers');
    Route::get('followings/{event_id}','AdminController@getFollowing');

    Route::get('liked/{event_id}','AdminController@getLikes');

    Route::get('user_event_liked_by/{event_id}','AdminController@getUserEventLikedBy');
    Route::get('event_liked_by/{event_id}','AdminController@getEventLikedBy');

    Route::get('user_event_rsvps/{event_id}','AdminController@getUserEventRsvps');
    Route::get('event_rsvps/{event_id}','AdminController@getEventRsvps');

    Route::get('user_event_members/{event_id}','AdminController@getUserEventMembers');
    Route::get('event_members/{event_id}','AdminController@getEventMembers');

    Route::get('user_details/{user_id}','AdminController@userDetails');

    Route::get('user_liked_events/{user_id}','AdminController@getLikeEvents');
    Route::get('user_rsvp_events/{user_id}','AdminController@getRsvpEvents');
    Route::get('user_comment_events/{user_id}','AdminController@getCommentedEvents');
    Route::get('user_shared_events/{user_id}','AdminController@getSharedEvents');

    Route::post('send_notification','AdminController@sendNotificion');
    Route::get('send_notification','AdminController@showNotificion');
    Route::get('events_map','AdminController@eventsMap');
    Route::get('users_map','AdminController@usersMap');
    Route::get('upload_csv','AdminController@csvUpload');

    Route::get('edit_event/{event_id}', 'AdminController@editEventView');

    /* Add event by admin */
    Route::get('add_event', 'AdminController@addEventAdminView');
    Route::post('add_event_admin', 'AdminController@addEventAdmin');

    Route::get('edit_user_event/{event_id}', 'AdminController@editUserEventView');

    Route::post('edit_event', 'AdminController@editEvent');

    Route::post('delete_event_cover_pic', 'AdminController@deleteEventCoverPic');
    Route::post('delete_attachment', 'AdminController@deleteAttachment');
    Route::post('invite_users', 'AdminController@inviteUsers');

    Route::post('save_attachment', 'AdminController@saveAttachment');
    Route::post('change_user_image', 'AdminController@changeUserImage');
});
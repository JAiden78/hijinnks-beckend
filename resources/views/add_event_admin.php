<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/head.php'; ?>

    <style>
        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
        }

        /* Modal Content (image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 40%;
            max-width: 700px;
        }

        /* Add Animation */
        .modal-content {
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @-webkit-keyframes zoom {
            from {-webkit-transform:scale(0)}
            to {-webkit-transform:scale(1)}
        }

        @keyframes zoom {
            from {transform:scale(0)}
            to {transform:scale(1)}
        }


        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px){
            .modal-content {
                width: 100%;
            }
        }

        /* Add Animation */
        @-webkit-keyframes slideIn {
            from {left: -1500px; opacity: 0}
            to {left: 0; opacity: 1}
        }

        @keyframes slideIn {
            from {left: -1500px; opacity: 0}
            to {left: 0; opacity: 1}
        }

        @-webkit-keyframes fadeIn {
            from {opacity: 0}
            to {opacity: 1}
        }

        @keyframes fadeIn {
            from {opacity: 0}
            to {opacity: 1}
        }

        /*.delete_attachment_video{*/
        /*background-color: #ed1d35;*/
        /*color: #fff;*/
        /*cursor: pointer;*/
        /*opacity: 1;*/
        /*line-height: 18px;*/
        /*border-radius: 50%;*/
        /*position: absolute;*/
        /*top: 2px;*/
        /*right: 37px;*/
        /*font-size: 0.9em;*/
        /*padding: 0px 8px 3px;*/
        /*text-align: center;*/
        /*font-size: 16px;*/
        /*}*/
        /*.delete_attachment_video:before*/
        /*{*/
        /*content: "x";*/
        /*}*/
        .delete_attachment_image{
            background-color: #ed1d35;
            color: #fff;
            cursor: pointer;
            opacity: 1;
            line-height: 18px;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            right: 5px;
            font-size: 0.9em;
            padding: 0px 8px 3px;
            text-align: center;
            font-size: 16px;
        }
        .delete_attachment_image:before
        {
            content: "x";
        }

        .profile-pic {
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .profile-pic:hover {opacity: 0.7;}



        .filelists {
            margin: 20px 0;
        }

        .filelists h5 {
            margin: 10px 0 0;
        }

        .filelists .cancel_all {
            color: red;
            cursor: pointer;
            clear: both;
            font-size: 10px;
            margin: 0;
            text-transform: uppercase;
        }

        .filelist {
            margin: 0;
            padding: 10px 0;
        }

        .filelist li {
            background: #fff;
            border-bottom: 1px solid #ECEFF1;
            font-size: 14px;
            list-style: none;
            padding: 5px;
            position: relative;
        }

        .filelist li:before {
            display: none !important;
        }
        /* main site demos */

        .filelist li .bar {
            background: #eceff1;
            content: '';
            height: 100%;
            left: 0;
            position: absolute;
            top: 0;
            width: 0;
            z-index: 0;
            -webkit-transition: width 0.1s linear;
            transition: width 0.1s linear;
        }

        .filelist li .content {
            display: block;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .filelist li .file {
            color: #455A64;
            float: left;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 50%;
            white-space: nowrap;
        }

        .filelist li .progress {
            color: #B0BEC5;
            display: block;
            float: right;
            font-size: 10px;
            text-transform: uppercase;
        }

        .filelist li .cancel {
            color: red;
            cursor: pointer;
            display: block;
            float: right;
            font-size: 10px;
            margin: 0 0 0 10px;
            text-transform: uppercase;
        }

        .filelist li.error .file {
            color: red;
        }

        .filelist li.error .progress {
            color: red;
        }

        .filelist li.error .cancel {
            display: none;
        }
        .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
            background-color: #1c3482;;
            color: white;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />

    <body>
        <div id="wrapper">
            <?php include 'includes/header.php'; ?>
            <main id="main">
                <?php include 'includes/sidebar.php'; ?>
                <div id="content">
                    <header class="header border">
                        <ul class="breadcrumbs list-none">
                            <li class="colored"><a href="<?= asset('adminlogin') ?>">Dashboard</a></li>
                            <?php if (isset($editUserEventBreadCrumb) && $editUserEventBreadCrumb == 1) { ?>
                                <li class="colored"><a href="<?= asset('users') ?>">Users</a></li>
                                <li class="colored"><a href="<?= asset('get_user_events/' . $event->user->id) ?>"><?= $event->user->username ?>'s Events</a></li>
                                <li><li>Edit Event</li></li>
                            <?php } else if (isset($editEventBreadCrumb) && $editEventBreadCrumb == 1) { ?>
                                <li class="colored"><a href="<?= asset('events') ?>">Events</a></li>
                                <li><li>Edit Event</li></li>
                            <?php } ?>
                        </ul>
                        <a href="#" class="btn-sidebar">&#9776;</a>
                    </header>
                    <div class="p-details">
                        <form action="<?= asset('add_event_admin') ?>" id="form" method="post" class="login-form change-pass" enctype="multipart/form-data">

                            <?php if (isset($editUserEventBreadCrumb) && $editUserEventBreadCrumb == 1) { ?>
                                <input name="breadCrumb" type="hidden" value="user">
                            <?php } else if (isset($addEventAdminBreadCrumb) && $addEventAdminBreadCrumb == 1) { ?>
                                <input name="breadCrumb" type="hidden" value="hijinks_event">
                            <?php } ?>
                            <input name="_token" type="hidden" value="<?= csrf_token() ?>">
                            <input name="localTimeZone" type="hidden">

                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h3 class="heading-txt colored" style="margin-top: 10px;">Add Event</h3>
                                        <?php
                                        if (Session::has('error')) {
                                            ?>
                                            <div class="alert alert-danger">
                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                                <?php echo Session::get('error') ?>
                                            </div>
                                        <?php } if (Session::has('success')) {
                                            ?>
                                            <div class="alert alert-success">
                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                                <?php echo Session::get('success') ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-5"></div>
                                    <div class="col-sm-2" style="text-align: center;">
                                        <?php $image = ($event->cover == null) ? asset('public/images/events/cover_photo_placeholder.jpg') : $event->cover ?>
                                        <div class="profile-img" id="preview" event-id="<?php echo $event->id; ?>" placeholder-image="<?php echo asset('public/images/events/cover_photo_placeholder.jpg'); ?>" image-name="<?php echo $image; ?>">
                                            <span>
                                                <figure class="event-figure" style="position:relative;">
                                                    <img src="<?= $image ?>" style="height: 150px; max-width: 160px; margin-left: auto; margin-right: auto; display: block;" alt="event Image" class="img-responsive"/>
                                                    <?php if ($event->cover != null) { ?>
                                                        <span class="delete_profile_pic"></span>
                                                    <?php } ?>
                                                </figure>
                                            </span>
                                        </div>
                                        <label class="choose-file"><input type="file" name="image" class="change-btn" id="change_profile_image">CHANGE PHOTO</label>
                                    </div>
                                    <div class="col-sm-5"></div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="colored">Title</label>
                                        <div class="input-fields">
                                            <input required name="title" type="text" value="<?= $event->title ?>"/>
                                        </div>
                                        <?php if ($errors->has('title')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('title') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="colored">Location</label>
                                        <div class="input-fields">
                                            <input required name="location" id="address" type="text" value="<?= $event->location ?>"/>
                                            <span id="lat-lng-block">
                                                <input name="lat" id="lat" type="hidden" value="<?= $event->lat ?>"/>
                                                <input name="lng" id="lng" type="hidden" value="<?= $event->lng ?>"/>
                                            </span>
                                        </div>
                                        <?php if ($errors->has('location')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('location') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="colored">Phone</label>
                                        <div class="input-fields">
                                            <input  name="phone_no" type="text" value="<?= $event->phone_no ?>"/>
                                        </div>
                                        <?php if ($errors->has('phone_no')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('phone_no') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="colored">Website</label>
                                        <div class="input-fields">
                                            <input  name="website_url" type="text" value="<?= $event->website_url ?>"/>
                                        </div>
                                        <?php if ($errors->has('website_url')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('website_url') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="colored">Start Time</label>
                                        <div style="border-radius: 4px; border: 2px solid #b7b8b7; border-top:0;">
                                            <div class='input-group date' id='start_time'>
                                                <input required type='text' name="start_time" class="form-control" />
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                            </div>
                                        </div>
                                        <?php if ($errors->has('start_time')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('start_time') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="colored">End Time</label>
                                        <div style="border-radius: 4px; border: 2px solid #b7b8b7; border-top:0;">
                                            <div class='input-group date' id='end_time'>
                                                <input required type='text' name="end_time" class="form-control" />
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                            </div>
                                        </div>
                                        <?php if ($errors->has('end_time')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('end_time') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="colored">Start Date</label>
                                        <div style="border-radius: 4px; border: 2px solid #b7b8b7; border-top:0;">
                                            <div class='input-group date' id='start_date'>
                                                <input required type='text' name="start_date" class="form-control" />
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        <?php if ($errors->has('start_date')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('start_date') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="colored">End Date </label>
                                        <div style="border-radius: 4px; border: 2px solid #b7b8b7; border-top:0;">
                                            <div class='input-group date' id='end_date'>
                                                <input required type='text' name="end_date" class="form-control" />
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        <?php if ($errors->has('end_date')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('end_date') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="colored">Description</label>
                                        <div class="input-fields">
                                            <textarea required name="description"><?= $event->description ?></textarea>
                                        </div>
                                        <?php if ($errors->has('description')) { ?>
                                            <div class="alert alert-danger">
                                                <?php foreach ($errors->get('description') as $message) { ?>
                                                    <?php echo $message; ?><br>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="colored">Event Type</label><br>
                                        Private <input type="radio" name="is_private" value="1" <?= $event->is_private == 1 ? 'checked' : '' ?>/>&nbsp;&nbsp;&nbsp;&nbsp;
                                        Public <input type="radio" name="is_private" value="0" <?= $event->is_private == 0 ? 'checked' : '' ?>/><br><br>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="colored">Interest</label><br>

                                        <select name="interest[]" class="form-control" multiple="">
                                            <?php
                                            if ($intrest_list) {
                                                foreach ($intrest_list as $key => $ivalue) {
                                                    ?>
                                                    <option value="<?= $ivalue->id ?>"><?= $ivalue->title ?></option>
                                                <?php
                                                }
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="colored">Is Recurring</label><br>
                                        Yes <input type="radio" class="is_reoccer" name="is_reoccuring" value="1" <?= $event->is_reoccuring == 1 ? 'checked' : '' ?>/>&nbsp;&nbsp;&nbsp;&nbsp;
                                        No <input type="radio" class="is_reoccer" name="is_reoccuring" value="0" <?= $event->is_reoccuring == 0 ? 'checked' : '' ?>/><br><br>
                                    </div>
                                </div> 
                                <div class="row" id="reocer_div" style="display: <?=$event->is_reoccuring == 1 ? 'block' : 'none'?>">
                                    <div class="col-sm-6">
                                        <label class="colored">Recurring Event</label><br>
                                        <select name="reoccure_type" class="form-control reocer_type">
                                            <option value="1" <?=($event->reoccure_type == '1')?'selected':''?>>Daily</option>
                                            <option value="2" <?=($event->reoccure_type == '2')?'selected':''?>>Weekly</option>
                                            <option value="3" <?=($event->reoccure_type == '3')?'selected':''?>>Monthly</option>
                                            <option value="4" <?=($event->reoccure_type == '4')?'selected':''?>>Yearly</option>                                    
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="colored">Every</label><br>
                                        <select name="reoccure" class="form-control">
                                            <?php
                                            $i = 1;
                                            while ($i < 13) {
                                                ?>
                                                <option value="<?= $i ?>" <?=$event->reoccure == $i?'selected':''?>> <?= $i ?> </option>  
                                                <?php
                                                $i++;
                                            } ?>
                                        </select>
                                        <div id="days" style="display: <?=($event->reoccure_type == '2')?'block':'none'?>">
                                            <label class="colored">Days</label><br>
                                            <?php
                                            $days = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
                                            ?>
                                            <select name="days[]" class="form-control days" multiple="">
                                                <?php
                                                    foreach ($days as $key => $value) { ?>
                                                <option value="<?=$value?>" <?= in_array($value, $reocer_list)?'selected':'';?>> <?=$value?> </option>  
                                                <?php    } ?> 
                                            </select>
                                        </div>
                                        
                                        <div id="years" style="display: <?=($event->reoccure_type == '4')?'block':'none'?>">
                                            <label class="colored">Years</label><br>
                                            <?php
                                            $years = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
                                            ?>
                                            <select name="years[]" class="form-control years" multiple="">
                                                <?php
                                                    foreach ($years as $key => $value) { ?>
                                                <option value="<?=$value?>" <?= in_array($value, $reocer_list)?'selected':'';?>> <?=$value?> </option>  
                                                <?php    } ?>  
                                            </select>
                                        </div>
                                        <div id="month" style="display: <?=($event->reoccure_type == '3')?'block':'none'?>">
                                            <label class="colored">Select Date</label><br>
                                        <div style="border-radius: 4px; border: 2px solid #b7b8b7; border-top:0;">
                                            <div class='input-group date' id='start_date'>
                                                <input type='text' name="reoccure_date" class="form-control" value="<?=$event->reoccure_end_date?>" />
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="colored">End Recurring</label><br>
                                        <select name="is_reoccuring_forever" class="form-control end_reoccer">
                                            <option value="1" <?=($event->is_reoccuring_forever == '1')?'selected':''?>>Forever</option>
                                            <option value="0"  <?=($event->is_reoccuring_forever == '0')?'selected':''?>>End Date</option>                                     
                                        </select>

                                    </div>
                                    <div class="col-sm-6" id="end_date_div" style="display:  <?=($event->is_reoccuring_forever == '0')?'block':'none'?>">
                                        <label class="colored">End Date</label><br>
                                        <div style="border-radius: 4px; border: 2px solid #b7b8b7; border-top:0;">
                                            <div class='input-group date' id='start_date'>
                                                <input type='text' name="reoccure_end_date" class="form-control" value="<?=$event->reoccure_end_date?>" />
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="colored">Attachments</label>
                                        <div id="fileuploader" style="width: 100%;" event-id="<?= $event->id ?>">Upload</div>
                                        <input type="hidden" name="attachments" id="files">
                                    </div>
                                </div>

                                <div class="container-fluid" style="display: none !important;">
                                    <ul class="nav nav-tabs" style="font-weight: bolder; border-bottom: 1px solid #1c3482;">
                                        <li class="active"><a href="#images-div" data-toggle="tab">Images</a></li>
                                        <li><a href="#videos-div" data-toggle="tab">Videos</a></li>
                                    </ul>

                                    <div class="tab-content ">
                                        <div class="tab-pane active" style="background-color: transparent;" id="images-div">
                                            <div class="row">
                                                <?php
                                                if (false) { //if ($attachments->isNotEmpty()) {
                                                    foreach ($attachments as $attachment) {
                                                        if ($attachment->type == 'image') {
                                                            ?>
                                                            <div class="col-md-3" style="padding: 5px;">
                                                                <figure class="event-figure" style="position:relative; overflow: hidden; height: 250px;">
                                                                    <a class="fancybox" style="height: 100%; width: 100%;" href="<?= asset('public/images/events/' . $attachment->attachment_path) ?>" data-fancybox-group="gallery">
                                                                        <img class="img-responsive" style="height: 100%; width: 100%; border: 1px solid silver;" src="<?= asset('public/images/events/' . $attachment->attachment_path) ?>"/>
                                                                    </a>
                                                                    <span class="delete_attachment_image" attachment-id="<?= $attachment->id ?>"></span>
                                                                </figure>
                                                            </div>
                                                        <?php
                                                        }
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="col-md-12" style="padding: 5px;">
                                                        <span>No Data Found</span>
                                                    </div>
<?php } ?>
                                            </div><hr>
                                        </div>
                                        <div class="tab-pane" style="background-color: transparent;" id="videos-div">
                                            <div class="row">
                                                <?php
                                                $videoCheck = 0;
                                                if (false) {
                                                    foreach ($attachments as $attachment) {
                                                        if ($attachment->type == 'video') {
                                                            $videoCheck = 1;
                                                            ?>
                                                            <div class="col-md-4" style="padding: 5px;">
                                                                <figure class="event-figure" style="position:relative; padding-top:15px; padding-bottom: 15px; text-align: center;">
                                                                    <video width="100%" height="285" controls style="object-fit: fill;">
                                                                        <source src="<?= asset('public/videos/events/' . $attachment->attachment_path) ?>" type="video/mp4">
                                                                        Your browser does not support the video tag.
                                                                    </video>
                                                                    <span class="delete_attachment_video" attachment-id="<?= $attachment->id ?>"></span>
                                                                </figure>
                                                            </div>
        <?php
        }
    }
} if ($videoCheck == 0) {
    ?>
                                                    <div class="col-md-12" style="padding: 5px;">
                                                        <span>No Data Found</span>
                                                    </div>
<?php } ?>
                                            </div><hr>
                                        </div>
                                    </div>
                                </div><br>

                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-12">
                                        <input type="submit" style="border-radius: 5px;" value="ADD EVENT">
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
<?php include 'includes/footer.php'; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js"></script>
    </body>
    <script>
        $('.fancybox').fancybox();
        $(document).ready(function () {

            $('body').on('change', '.is_reoccer', function () {
                var val = $(this).val();

                if (val == '1') {
                     $('#reocer_div').show(); 
                }  else {
                    $('.reocer_type').val('');
                    $('.reoccure').val('');
                    $('.days').val('');
                    $('.years').val('');
                    $('#reocer_div').hide(); 
                }  
            });

            $('body').on('change', '.reocer_type', function () {
                var val = $(this).val(); 
                if (val == '4') {
                    
                    $('#years').show();
                     $('#days').hide();
                     $('#month').hide();
                }  else if (val == '2') {
                     $('#days').show();
                    $('#years').hide();
                     $('#month').hide();
                }  else if (val == '3') {
                     $('#month').show();
                    $('#days').hide();
                    $('#years').hide();
                } else {
                     $('#month').hide();
                     $('#years').hide();
                     $('#days').hide();
                }
            });
            $('body').on('change', '.end_reoccer', function () {
                if ($(this).val() == '0') {
                    $('#end_date_div').show();
                } else {
                    $('#end_date_div').hide();
                }
            });


            $('select').select2();
            var serverTimezone = 'UTC' //set js variable for server timezone
            var localTimeZone = jstz.determine(); //this will fetch user's timezone

            var utcEventStart = '<?php echo $event->utc_event_time; ?>'; //set js variable for updated_at
            var utcEventEnd = '<?php echo $event->utc_event_end_date; ?>'; //set js variable for updated_at

            var startTimeObj = moment.tz(utcEventStart, serverTimezone); //create moment js time object for server time
            var endTimeObj = moment.tz(utcEventEnd, serverTimezone); //create moment js time object for server time

            var start = startTimeObj.clone().tz(localTimeZone.name()).format('MMMM Do YYYY, h:mm a'); //convert server time to local time of user
            start = start.split(',');
            var startingDate = start[0];
            var startingTime = start[1];

            var end = endTimeObj.clone().tz(localTimeZone.name()).format('MMMM Do YYYY, h:mm a'); //convert server time to local time of user
            end = end.split(',');
            var endingDate = end[0];
            var endingTime = end[1];

            $('input[name=localTimeZone]').val(localTimeZone.name());
            $('input[name=start_time]').val(startingTime);
            $('input[name=end_time]').val(endingTime);
            $('input[name=start_date]').val(startingDate);
            $('input[name=end_date]').val(endingDate);

            var event_id = '<?php echo $event->id ?>';
            var attachments = new Array();
            $("#fileuploader").uploadFile({
                url: "../resources/views/includes/upload_attachment.php",
                multiple: true,
                dragDrop: true,
                sequential: true,
                showDelete: true,
                maxFileSize: 30720 * 1024,
                fileName: "myfile",
                acceptFiles: "image/*,video/*",
                showPreview: true,
                previewHeight: "100px",
                previewWidth: "100px",
                onSelect: function (files)
                {
                    $('input[type=submit]').attr('disabled', 'disabled');
                    for (var i = 0; i < files.length; i++)
                    {
                        if (files[i].type == 'image/png' || files[i].type == 'image/jpg' || files[i].type == 'image/jpeg' || files[i].type == 'bmp' || files[i].type == 'video/mp4')
                        {
                            //Do nothing
                        } else
                        {
                            alert('Only allowed formats are jpg,jpeg,png,bmp and mp4. You have to try again !');
                            return false;
                        }
                    }
                    return true;
                },
                onSuccess: function (files, data, xhr, pd)
                {
                    var attachmentName = JSON.stringify(data);
                    attachments.push(attachmentName);
                    $('#files').val(attachments);
                    $('input[type=submit]').removeAttr('disabled');
                    $('input[type=submit]').removeAttr('abc');
                },
                deleteCallback: function (data, pd) {
                    $.post("../resources/views/includes/delete_attachment.php", {op: "delete", name: data});
                    var files = $('#files').val();
                    if (files.indexOf(data) >= 0)
                    {
                        var ret = files.replace(data, '');
                        $('#files').val(ret);
                    }
                }
            });

            $('#address').geocomplete({
                details: "#lat-lng-block"
            });
            $('#myTableUser').DataTable();
<?php if (isset($editUserEventBreadCrumb) && $editUserEventBreadCrumb == 1) { ?>
                $('.menu>li>#users').addClass('active');
<?php } else if (isset($editEventBreadCrumb) && $editEventBreadCrumb == 1) { ?>
                $('.menu>li>#events').addClass('active');
<?php } ?>
        });

        $('#start_time, #end_time').datetimepicker({
            format: 'LT'
        });
        $('#start_date, #end_date').datetimepicker({
            format: 'MMMM Do YYYY'
        });

        function handleProfilePicSelect(event)
        {
            var input = this;
            if (input.files[0].size < 2000000) {
                if (input.files && input.files[0])
                {
                    var reader = new FileReader();
                    reader.readAsDataURL(input.files[0]);
                    reader.onload = (function (e)
                    {
                        var fileExtension = ['jpeg', 'jpg', 'png', 'bmp'];
                        if ($.inArray($('#change_profile_image').val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                            alert("Only formats are allowed : " + fileExtension.join(', '));
                            $('#change_profile_image').val('');
                        } else {
                            var span = document.createElement('span');
                            span.innerHTML = ['<figure class="event-figure" style="position:relative;"><img src="', e.target.result, '" style="height: 150px; max-width: 160px; margin-left: auto; margin-right: auto; display: block;" alt="Event Image" class="img-responsive"/> <span class="remove_profile_preview"></span></figure>'].join('');
                            $('#preview').html('');
                            document.getElementById('preview').insertBefore(span, null);
                        }
                    });
                }
            } else {
                alert("The file does not match the upload conditions, The maximum file size for uploads should not exceed 2MB");
            }
        }
        $('#preview').on('click', '.remove_profile_preview', function ()
        {
            var parent = $(this).parent('figure').parent('span').parent('#preview');
            var oldImage = parent.attr('image-name');
            $(this).prev(".img-responsive").attr('src', oldImage);
            $('input[name=image]').val("");
            if (parent.attr('image-name') == parent.attr('placeholder-image'))
            {
                $(this).remove();
            } else
            {
                $(this).removeClass("remove_profile_preview").addClass("delete_profile_pic");
            }
        });

        $('#preview').on('click', '.delete_profile_pic', function ()
        {
            var result = confirm("Are you sure that you want to delete this profile picture?");
            if (result == true) {
                var parent = $(this).parent('figure').parent('span').parent('#preview');
                var placeholderImage = parent.attr('placeholder-image');
                parent.attr('image-name', placeholderImage);
                $(this).prev(".img-responsive").attr('src', placeholderImage);
                $(this).remove();
                $('input[name=image]').val("");
                event_id = parent.attr('event-id');
                $.ajax({
                    type: "POST",
                    url: "<?php echo url('delete_event_cover_pic'); ?>",
                    data: {'event_id': event_id},
                    dataType: 'json',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    }
                });
            }
        });

        $('.profile-pic').click(function () {
            var attachmentId = $(this).attr('attachment-id');
            var modal = document.getElementById('modal-' + attachmentId);
            var modalImg = document.getElementById("modal-image-" + attachmentId);
            modal.style.display = "block";
            modalImg.src = this.src;
        });

        $('.delete_attachment_image, .delete_attachment_video').click(function ()
        {
            var result = confirm("Are you sure that you want to delete this attachment?");
            if (result == true) {
                attachment_id = $(this).attr('attachment-id');
                $.ajax({
                    type: "POST",
                    url: "<?php echo url('delete_attachment'); ?>",
                    data: {'attachment_id': attachment_id},
                    dataType: 'json',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    }
                });
                $(this).parent('figure').parent('div').remove();
            }
        });
        $('#change_profile_image').change(handleProfilePicSelect);
    </script>
</html>
<!DOCTYPE html>
<html lang="en">

    <?php include 'includes/head.php'; ?>
    <style>
        .multiselect-container input[type="checkbox"] {
            -webkit-appearance: checkbox;
        }
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
            width: 63%;
            max-width: 700px;
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 30;
            right: 25%;
            color: white;
            font-size: 40px;
            font-weight: bolder;
            transition: 0.3s;
            opacity: 1;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
            opacity: 0.5;
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

        time.icon
        {
            font-size: 1em; /* change icon size */
            display: block;
            position: relative;
            width: 7em;
            height: 7em;
            background-color: #fff;
            border-radius: 0.6em;
            box-shadow: 0 1px 0 #bdbdbd, 0 2px 0 #fff, 0 3px 0 #bdbdbd, 0 4px 0 #fff, 0 5px 0 #bdbdbd, 0 0 0 1px #bdbdbd;
            overflow: hidden;
        }
        time.icon *
        {
            display: block;
            width: 100%;
            font-size: 1em;
            font-weight: bold;
            font-style: normal;
            text-align: center;
        }
        time.icon strong
        {
            position: absolute;
            top: 0;
            padding: 0.4em 0;
            color: #fff;
            background-color: #fd9f1b;
            border-bottom: 1px dashed #f37302;
            box-shadow: 0 2px 0 #fd9f1b;
        }
        time.icon em
        {
            position: absolute;
            bottom: 0.3em;
            color: #fd9f1b;
        }
        time.icon span
        {
            font-size: 2.8em;
            letter-spacing: -0.05em;
            padding-top: 1.2em;
            color: #2f2f2f;
        }
        .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
            background-color: #1c3482;;
            color: white;
        }
    </style>
    <body>
        <div id="wrapper" style="overflow: auto">

            <?php include 'includes/header.php'; ?>

            <main id="main" style="overflow: auto">

                <?php include 'includes/sidebar.php'; ?>
                <div id="content">
                    <div class="p-details">
                        <header class="header">
                            <ul class="breadcrumbs list-none">
                                <li class="colored"><a href="<?= asset('adminlogin') ?>">Dashboard</a></li>
                                <?php if (isset($userBreadCrumb) && $userBreadCrumb == 1) { ?>
                                    <li class="colored"><a href="<?= asset('users') ?>">Users</a></li>
                                    <li class="colored"><a href="<?= asset('get_user_events/' . $event->user->id) ?>"><?= $event->user->username . '\'s events' ?></a></li>
                                <?php } else if (isset($eventBreadCrumb) && $eventBreadCrumb == 1) { ?>
                                    <li class="colored"><a href="<?= asset('events') ?>">Events</a></li>
                                <?php } ?>
                                <li><?= $event->title ?></li>
                            </ul>
                            <?php if (isset($userBreadCrumb) && $userBreadCrumb == 1) { ?>
                                <a class="btn btn-primary" type="button" style="float: right; width: 200px;" href="<?= asset('edit_user_event/' . $event->id) ?>">Edit Event</a>
                            <?php } else if (isset($eventBreadCrumb) && $eventBreadCrumb == 1) { ?>
                                <a class="btn btn-primary" type="button" style="float: right; width: 200px;" href="<?= asset('edit_event/' . $event->id) ?>">Edit Event</a>
                            <?php } ?><br>
                        </header>
                        <?php
                        $coverCheck = 0;
                        if (isset($event->cover) && $event->cover != '') {
//                  $cover = asset('public/images/events/'.$event->cover);
                            $cover = $event->cover;
                            $coverCheck = 1;
                        } else {
                            $coverCheck = 0;
                            $cover = asset('public/images/events/cover_photo_placeholder_detail_page.jpg');
                        }
                        ?>
                        <div class="container-fluid cover-pic" src="<?php echo $cover; ?>" style="background-color: #1f1f1f; background-image: url(<?php echo $cover; ?>); background-position: center;  background-repeat: no-repeat; background-size: <?= $coverCheck == 1 ? 'contain' : 'cover' ?>; height: 400px; opacity: 0.85; position: relative;">
                            <div style="position: absolute; bottom: 50px; left: 20px;">
                                <time datetime="" class="icon">
                                    <em id="calender-day"></em>
                                    <strong id="calender-month"></strong>
                                    <span id="calender-date"></span>
                                </time>
                            </div>
                            <div style="position: absolute; bottom: 50px; left: 150px; color: white;">
                                <h1 style="font-weight: bolder;"><?= $event->title ?></h1>
                                <h4 style="font-size: 17px;"><i class="<?= $event->is_private == 0 ? 'fa fa-unlock' : 'fa fa-lock' ?>"></i> <?= $event->is_private == 0 ? 'Public' : 'Private' ?></h4>
                            </div>
                        </div>
                        <div class="container-fluid" style="border-bottom: 2px solid silver; margin: 15px 5px 15px 5px; padding: 0px 30px 15px 30px;">
                            <div class="row">
                                <div class="col-sm-2 col-md-1">
                                    <a href="<?php echo asset('user_details/' . $event->user->id); ?>">
                                        <img src="<?= $event->user->photo ?>" class="img-responsive">
                                    </a>
                                </div>
                                <div class="col-sm-3 col-md-4" style="padding-left: 0px;">
                                    <h4 class="colored" style="color: #959595; font-weight: bolder; margin: 15px auto 0px auto;">Hosted By:</h4>
                                    <a href="<?php echo asset('user_details/' . $event->user->id); ?>">
                                        <h3 style="color: black; font-weight: bolder; margin: 0px auto auto auto;"><?= $event->user->username ?></h3>
                                    </a>
                                </div>
                                <div class="col-sm-7" style="text-align: right;">
                                    <p style="text-align: right;"><span class="colored">Event Views:</span> <?= $event->view_count ?></p>
                                    <h4><i class="fa fa-phone"></i> <span class="colored">Phone:</span> <small><?= isset($event->phone_no) ? $event->phone_no : 'N/A' ?>, &nbsp;&nbsp;&nbsp;&nbsp; <span class="colored">Count:</span> <?= $event->phone_view_count ?></small></h4>
                                    <h4><i class="fa fa-globe"></i> <span class="colored">Website:</span> <small>
<?php
if (isset($event->website_url)) {
    $website_url = $event->website_url;
    if (strlen($website_url) > 30) {
        $website_url = substr($website_url, 0, 30) . '...';
    }
    ?>
                                                <a href="//<?= $event->website_url ?>"><?= $website_url ?></a>,
                                            <?php } else { ?>
                                                N/A,
                                            <?php } ?>
                                            &nbsp;&nbsp;&nbsp;&nbsp; <span class="colored">Count:</span> <?= $event->website_view_count ?></small></h4>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div style="border-bottom: 1px solid #dddddd; padding-left: 25px; padding-right: 25px;">
                                <h4 class="colored">Description:</h4>
                                <p style="color: #000; font-weight: bold;"><?= $event->description ?></p>
                            </div>
                            <div style="border-bottom: 1px solid #dddddd; padding-left: 25px; padding-right: 25px;">
                                <div class="row"><br>
                                    <div class="col-md-5">
                                        <h4 class="colored"><i class="fa fa-map-marker-alt"></i> Location:</h4>
                                        <p style="color: #000; font-weight: bold;"><?= $event->location ?></p>
                                        <div id="map" style="height: 200px;width: 100%"></div><br>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-6">
                                        <h4><i class="fa fa-clock-o"></i> <span class="colored">Event Date & Time:</span></h4>
                                        <p style="color: #000; font-weight: bold;" id="event-date-time"></p><br>
                                        <div class="colored">
<?php if (isset($userBreadCrumb) && $userBreadCrumb == 1) { ?>
                                                <a href="<?php echo asset('user_event_members/' . $event->id); ?>">
                                                    <h4 style="display: inline;"><i class="fa fa-user colored"></i> Members: <?= $event->Invites->count() ?></h4>
                                                </a>
<?php } else if (isset($eventBreadCrumb) && $eventBreadCrumb == 1) { ?>
                                                <a href="<?php echo asset('event_members/' . $event->id); ?>">
                                                    <h4 style="display: inline;"><i class="fa fa-user colored"></i> Members: <small style="font-weight: bold;"><?= $event->Invites->count() ?></small></h4>
                                                </a>
<?php } ?><br><br>

                                            <?php if (isset($userBreadCrumb) && $userBreadCrumb == 1) { ?>
                                                <a href="<?php echo asset('user_event_rsvps/' . $event->id); ?>">
                                                    <h4 style="display: inline;"><i class="fa fa-user colored"></i> RSVP: <small style="font-weight: bold;"><?= $event->arrived->count() ?></small></h4>
                                                </a>
<?php } else if (isset($eventBreadCrumb) && $eventBreadCrumb == 1) { ?>
                                                <a href="<?php echo asset('event_rsvps/' . $event->id); ?>">
                                                    <h4 style="display: inline;"><i class="fa fa-user colored"></i> RSVP: <small style="font-weight: bold;"><?= $event->arrived->count() ?></small></h4>
                                                </a>
<?php } ?><br><br>

                                            <?php if (isset($userBreadCrumb) && $userBreadCrumb == 1) { ?>
                                                <a href="<?php echo asset('user_event_liked_by/' . $event->id); ?>">
                                                    <h4 style="display: inline;"><i class="fa fa-user colored"></i> Liked By: <small style="font-weight: bold;"><?= $event->likes->count() ?></small></h4>
                                                </a>
<?php } else if (isset($eventBreadCrumb) && $eventBreadCrumb == 1) { ?>
                                                <a href="<?php echo asset('event_liked_by/' . $event->id); ?>">
                                                    <h4 style="display: inline;"><i class="fa fa-user colored"></i> Liked By: <small style="font-weight: bold;"><?= $event->likes->count() ?></small></h4>
                                                </a>
<?php } ?><br><br>
                                            <?php
                                            $reoccurType='';
                                            $type='';
                                            if ($event->is_reoccuring == 1) {
                                                $carbon_date = Carbon\Carbon::parse($event->event_date);
                                                $end_re_date = $carbon_date;
                                                if ($event->reoccure_type == 1) {
                                                    $reoccurType = "Daily";
                                                    $type = "Days";
                                                    $end_re_date = $carbon_date->addDays($event->reoccure);
                                                } else if ($event->reoccure_type == 7) {
                                                    $reoccurType = "Weekly";
                                                    $type = "Weeks";
                                                    $end_re_date = $carbon_date->addWeeks($event->reoccure);
                                                    ;
                                                } else if ($event->reoccure_type == 30) {
                                                    $reoccurType = "Monthly";
                                                    $type = "Months";
                                                    $end_re_date = $carbon_date->addMonths($event->reoccure);
                                                } else if ($event->reoccure_type == 360) {
                                                    $reoccurType = "Yearly";
                                                    $type = "Years";
                                                    $end_re_date = $carbon_date->addYears($event->reoccure);
                                                }
                                                ?>
                                                <h4><span class="colored">Reocurrance Type: <?= $reoccurType ?></span></h4><br>
                                                <h4><span class="colored">Number of <?= $type ?>: <?= $event->reoccure ?></span></h4><br>
                                                <?php if ($event->is_reoccuring_forever == 1) { ?> 
                                                    <h4><span class="colored">Recurring Forever: Yes</span></h4><br>
                                                <?php } else { ?>
                                                    <h4><span class="colored">Recurrance End Date: <?= date('M ,d Y', strtotime($end_re_date)) ?></span></h4>
                                                <?php }
                                            } ?>
                                        </div><br><br>

                                    </div>
                                </div>
                            </div>
                        </div><br><br>
                        <div class="container-fluid">
                            <div class="col-md-5">
                                <h4 class="colored"><i class="fa fa-map-marker-alt"></i> Invite User:</h4>
                                <form method="post" action="<?= asset('invite_users')?>">
                                    <?= csrf_field()?>
                                    
                                    <select id="example-getting-started" multiple="multiple" name="user_id[]"> 
                                    <?php foreach ($users as $user){ ?>
                                    <option value="<?= $user->id?>"><?= $user->username?></option>
                                    <?php } ?>
                                </select>
                                    <input type="hidden" name="id" value="<?= $event->id?>">
                                    <input type="submit" value="Invite" class="btn btn-info" style="margin-left: 10px; width: 100px;">
                                    </form>
                            </div>
                        </div><br><br>
                        <div id="cover-pic-modal" class="modal">
                            <span class="close">&times;</span>
                            <img class="modal-content" id="modal-image">
                        </div>

                        <div class="container">
                            <div class="container-fluid">
                                <ul class="nav nav-tabs" style="font-weight: bolder; border-bottom: 1px solid #1c3482;">
                                    <li class="active"><a href="#images-div" data-toggle="tab">Images</a></li>
                                    <li><a href="#videos-div" data-toggle="tab">Videos</a></li>
                                </ul>

                                <div class="tab-content ">
                                    <div class="tab-pane active" style="background-color: transparent;" id="images-div">
                                        <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                            <?php
                                            if ($attachments->isNotEmpty()) {
                                                foreach ($attachments as $attachment) {
                                                    if ($attachment->type == 'image') {
                                                        ?>
                                                        <div class="col-md-3" style="padding: 5px;">
                                                            <figure class="event-figure" style="position:relative; overflow: hidden; height: 250px;">
                                                                <a class="fancybox" href="<?= asset('public/images/events/' . $attachment->attachment_path) ?>" data-fancybox-group="gallery">
                                                                    <img class="img-responsive" style="height: 100%; width: 100%; border: 1px solid silver;" src="<?= asset('public/images/events/' . $attachment->attachment_path) ?>"/>
                                                                </a>
                                                            </figure>
                                                        </div>
        <?php }
    }
} else { ?>
                                                <div class="col-md-12" style="padding: 5px;">
                                                    <span>No Data Found</span>
                                                </div>
<?php } ?>
                                        </div><hr>
                                    </div>
                                    <div class="tab-pane" style="background-color: transparent;" id="videos-div">
                                        <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                            <?php
                                            $videoCheck = 0;
                                            if ($attachments->isNotEmpty()) {
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
                                                            </figure>
                                                        </div>
        <?php }
    }
} if ($videoCheck == 0) { ?>
                                                <div class="col-md-12" style="padding: 5px;">
                                                    <span>No Data Found</span>
                                                </div>
<?php } ?>
                                        </div><hr>
                                    </div>
                                </div>
                            </div>
                        </div><br>
                    </div>
                </div>
            </main>
        </div>
<?php include 'includes/footer.php'; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.js" rel="javascript"></script>
    <!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js" rel="javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js.map" rel="javascript"></script>-->
        <script type="text/javascript">
            $(document).ready(function () {
                $('#example-getting-started').multiselect({
                    enableFiltering: true,
                    includeSelectAllOption: true,
                    buttonWidth: '300px',
                    maxHeight: '200',
                    dropUp: true,
                    enableCaseInsensitiveFiltering: true
                });
            });
        </script>
    </body>

    <script>
        $('.fancybox').fancybox();
        $(document).ready(function () {

    //        var serverTimezone = ' //echo date_default_timezone_get(); //' //set js variable for server timezone
            var serverTimezone = 'UTC' //set js variable for server timezone
            var localTimeZone = jstz.determine(); //this will fetch user's timezone

            var utcEventStart = '<?php echo $event->utc_event_time; ?>'; //set js variable for updated_at
            var utcEventEnd = '<?php echo $event->utc_event_end_date; ?>'; //set js variable for updated_at

            var startTimeObj = moment.tz(utcEventStart, serverTimezone); //create moment js time object for server time
            var endTimeObj = moment.tz(utcEventEnd, serverTimezone); //create moment js time object for server time

            var start = startTimeObj.clone().tz(localTimeZone.name()).format('MMMM Do YYYY, h:mm A'); //convert server time to local time of user
            start = start.split(',');
            var startingDate = start[0];
            var startingTime = start[1];

            var end = endTimeObj.clone().tz(localTimeZone.name()).format('MMMM Do YYYY, h:mm A'); //convert server time to local time of user
            end = end.split(',');
            var endingDate = end[0];
            var endingTime = end[1];

            var startForCalender = startTimeObj.clone().tz(localTimeZone.name()).format(); //convert server time to local time of user
            var weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            var date = new Date(startForCalender);
            var newDate = date.toString('yyyy-MM-dd');
            $('time').attr('datetime', newDate);

            var calenderMonth = monthNames[date.getMonth()];
            var calenderDate = newDate.split('-');
            var calenderDate = calenderDate[2];
            var calenderDay = weekday[date.getDay()];

            $('#calender-month').html(calenderMonth);
            $('#calender-date').html(calenderDate);
            $('#calender-day').html(calenderDay);

            $('#starting-from').html(startingDate);
            $('#ending-at').html(endingDate);
            $('#time').html(startingTime + " - " + endingTime);

            $('#event-date-time').html(startingDate + ", " + endingDate + " at " + startingTime + " to " + endingTime);

<?php if (isset($userBreadCrumb) && $userBreadCrumb == 1) { ?>
                $('.menu>li>#users').addClass('active');
<?php } else if (isset($eventBreadCrumb) && $eventBreadCrumb == 1) { ?>
                $('.menu>li>#events').addClass('active');
<?php } ?>

        });

        $('.cover-pic').click(function () {
            var modal = document.getElementById('cover-pic-modal');
            var modalImg = document.getElementById("modal-image");
            modal.style.display = "block";
            modalImg.src = $(this).attr('src');
        });

        $('.close').click(function () {
            $(this).parent('.modal').css('display', 'none');
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

        var eventlocation = <?php echo json_encode($event); ?>;
        var username = '<?php echo json_encode($event->user->username); ?>';
        var map;
        var markers = [];

        function initMap() {

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 20,
                center: new google.maps.LatLng(41.850033, -87.6500523),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var marker, i;

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(eventlocation.lat, eventlocation.lng),
                map: map,
                title: eventlocation.title
            });
            map.setCenter(marker.getPosition());

            if (eventlocation.cover === '')
            {
                var photo = '<?php echo asset("public/images/events/cover_photo_placeholder.jpg"); ?>';
            } else
            {
                var photo = eventlocation.cover;
            }
            var eventDetailsHref = '<?php echo asset('event_details'); ?>/' + eventlocation.id;
            var userDetailsHref = '<?php echo asset('user_details'); ?>/' + eventlocation.user_id;
            var title = eventlocation.title;
            var username = username;
            var location = eventlocation.location;

            var content = '<div class="marker-content-container"><a href="' + eventDetailsHref + '"><img alt="Event\'s cover pic" class="marker-thumbnail" src="' + photo + '"></a><br>Event Title: <a class="clickable" href="' + eventDetailsHref + '">' + title + '</a>' + '<br>Hosted By: <a class="clickable" href="' + userDetailsHref + '">' + username + '</a>' + '<br><span>Location: ' + location + '</span></div>';
            var infowindow = new google.maps.InfoWindow();
            google.maps.event.addListener(marker, 'click', (function (marker, content, infowindow) {
                return function () {
                    infowindow.setContent(content);
                    infowindow.open(map, marker);
                };
            })(marker, content, infowindow));
        }
        function addMarkerInfo(location) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(location.hotspot_lat, location.hotspot_long),
                map: map
            });
            markers.push(marker);
        }
        function deleteMarkers() {
            clearMarkers();
            markers = [];
        }
        function clearMarkers() {
            setMapOnAll(null);
        }
        function setMapOnAll(map) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7l5RqKcybbGrvSVnI2siEFFuv-VqkuZY&callback=initMap">
    </script>
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7l5RqKcybbGrvSVnI2siEFFuv-VqkuZY&callback=initMap">
</script>
</html>
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






    .profile-pic {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .profile-pic:hover {opacity: 0.7;}

</style>
<script>
    function eventDateTime(id, eventStart, eventEnd) {
        var serverTimezone = 'UTC' //set js variable for server timezone
        var localTimeZone = jstz.determine(); //this will fetch user's timezone

        var utcEventStart = eventStart;
        var utcEventEnd = eventEnd;

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

        $('#event-date-'+id).html(startingDate + ' - ' + endingDate);
        $('#event-time-'+id).html(startingTime + ' - ' + endingTime);
    }
</script>
<body>
<div id="wrapper">

    <?php include 'includes/header.php'; ?>

    <main id="main">

        <?php include 'includes/sidebar.php'; ?>
        <div id="content">
            <header class="header">
                <ul class="breadcrumbs list-none">
                    <li class="colored"><a href="main.html">Dashboard</a></li>
                    <li class="colored"><a href="<?= asset('users')?>">Users</a></li>
                    <li><?= $user->username.'\'s events' ?></li>
                </ul>
                <a href="#" class="btn-sidebar">&#9776;</a>
            </header>
            <div class="content-area">
                <div id="map" style="height: 400px;width: 100%"></div><br>
                <form action="<?= asset('get_user_events') ?>" id="listing_filter_form" method="post">
                    <input name="_token" type="hidden" value="<?= csrf_token() ?>">
                    <input name="user_id" type="hidden" value="<?= $user->id ?>">
                    <select name="listing_filter" id="listing_filter">
                        <option value="1" <?= $listing_filter == 1 ? 'selected' : '' ?>>All Events</option>
                        <option value="2" <?= $listing_filter == 2 ? 'selected' : '' ?>>Old Events</option>
                        <option value="3" <?= $listing_filter == 3 ? 'selected' : '' ?>>Ongoing Events</option>
                        <option value="4" <?= $listing_filter == 4 ? 'selected' : '' ?>>Upcoming Events</option>
                    </select>
                </form>
                <?php if (Session::has('success')) {
                    ?>
                    <div class="alert alert-success">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                        <?php echo Session::get('success') ?>
                    </div>
                <?php } ?>
                <div class="search-area add">
                    <div class="search-query">
                        <!--<strong>Searcconh Events</strong>-->
                        <!--                                <form action="#">
                                                            <fieldset>
                                                                <input type="submit" value="submit">
                                                                <input type="search" placeholder="" id="myInput" onkeyup="myFunction()">
                                                            </fieldset>
                                                        </form>-->
                    </div>
                </div>
                <div class="table-scroll">
                    <table id="myTableEvent">
                        <thead>
                        <tr>
                            <th style="width: 10px; text-align: center;">Sr</th>
                            <th>Title</th>
                            <th>Cover Photo</th>
                            <th>Event Date</th>
                            <th>Event Time</th>
                            <th>Location</th>
                            <!--<th>Status</th>-->
                            <th>Members</th>
                            <th>RSVP</th>
                            <th>Likes</th>
                            <th>Viewed</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=1; foreach ($events as $event) {
                            $image = asset('public/images/events/cover_photo_placeholder.jpg');
                            if($event->cover){
                                $image = $event->cover;
                            }
                            ?>
                            <tr>
                                <td><?php echo $i;$i++; ?></td>
                                <td class="colored"><a href="<?= asset('user_event_details/'.$event->id) ?>" class="clickable"><?= $event->title ?></a></td>
                                <td class="img-holder"><img event-id="<?= $event->id ?>" alt="Cover Pic" class="profile-pic img-responsive" src="<?= $image ?>"></td>
                                <td  id="event-date-<?= $event->id ?>"></td>
                                <td id="event-time-<?= $event->id ?>"></td>
                                <script>
                                    var eventId = '<?php echo $event->id ?>';
                                    var eventStart = '<?php echo $event->utc_event_time ?>';
                                    var eventEnd = '<?php echo $event->utc_event_end_date ?>';
                                    eventDateTime(eventId, eventStart, eventEnd);
                                </script>
                                <td><?= $event->location ?></td>
                                <!--<td>NO</td>-->
                                <?php if(Auth::user()->user_type == 1){ ?>
                                    <td><a class="clickable" href="<?php if($event->Invites->count() > 0 ){ echo asset('user_event_members/'.$event->id);} else{ echo '#';}?>"><?= $event->Invites->count() ?></a></td>
                                <?php } else { ?>
                                    <td><?= $event->Invites->count() ?></td>
                                <?php } ?>
                                <?php if(Auth::user()->user_type == 1){ ?>
                                    <td><a class="clickable" href="<?php if($event->arrived->count() > 0 ){ echo asset('user_event_rsvps/'.$event->id);} else{ echo '#';}?>"><?= $event->arrived->count() ?></a></td>
                                    <td><a class="clickable" href="<?php if($event->likes->count() > 0 ){ echo asset('user_event_liked_by/'.$event->id);} else{ echo '#';}?>"><?= $event->likes->count() ?></a></td>
                                <?php } else { ?>
                                    <td><?= $event->arrived->count() ?></td>
                                    <td><?= $event->likes->count() ?></td>
                                <?php } ?>
                                <td><?= $event->view_count ?></td>
                                <td>
                                    <a href="<?= asset( 'edit_user_event/'. $event->id ) ?>" title="Edit Event"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                    <a href="#event<?= $event->id ?>" class="btn-popup"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </td>
                            </tr>

                            <div id="modal-<?= $event->id ?>" class="modal">
                                <span class="close">&times;</span>
                                <img class="modal-content" id="modal-image-<?= $event->id ?>">
                            </div>

                            <div id="event<?= $event->id ?>" class="dialogue">
                                <div class="dialogue-holder">
                                    <div class="confirm-msg">
                                        <div class="confirm-txt">
                                            <header class="header">
                                                <h2>Delete Event</h2>
                                                <a href="#" class="btn-close">x</a>
                                            </header>
                                            <div class="txt">
                                                <img src="<?= asset('assets/images/img10.png') ?>" alt="Danger">
                                                <p>Are you sure you want to delete event?</p>
                                                <div class="btns">
                                                    <a href="#" class="btn-primary cancel">Cancel</a>
                                                    <a href="<?= asset('delete_event/' . $event->id) ?>" class="btn-primary delete">Delete Event</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>

<!--https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css-->
<!--https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js-->
</body>
<script>
    $('#listing_filter').change(function () {
        $('#listing_filter_form').submit();
    });
    $(document).ready(function(){
        $('#myTableEvent').DataTable();
        $('.menu>li>#users').addClass('active');
    });
    $('.profile-pic').click(function(){
        var eventId = $(this).attr('event-id');
        var modal = document.getElementById('modal-'+eventId);
        var modalImg = document.getElementById("modal-image-"+eventId);
        modal.style.display = "block";
        modalImg.src = this.src;
    });
    $('.close').click(function(){
        $(this).parent('.modal').css('display', 'none');
    });
    var locations = <?php echo json_encode($events); ?>;
    var map;
    var markers = [];

    function initMap() {

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 2,
            center: new google.maps.LatLng(41.850033, -87.6500523),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var marker, i;
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i].lat, locations[i].lng),
                map: map,
                title: locations[i].title
            });
            if(locations[i].cover === '')
            {
                var photo = '<?php echo asset("public/images/events/cover_photo_placeholder.jpg"); ?>';
            }
            else
            {
                var photo = locations[i].cover;
            }

            var eventDetailsHref = '<?php echo asset('user_event_details'); ?>/'+locations[i].id;
            var userDetailsHref = '<?php echo asset('user_details'); ?>/'+locations[i].user_id;
            var title = locations[i].title;
            var username = '<?php echo $user->username; ?>';
            var location = locations[i].location;
            var content = '<div class="marker-content-container"><a href="'+eventDetailsHref+'"><img alt="Event\'s cover pic" class="marker-thumbnail" src="'+photo+'"></a><br>Event Title: <a class="clickable" href="'+eventDetailsHref+'">'+title+'</a>'+'<br>Hosted By: <a class="clickable" href="'+userDetailsHref+'">'+username+'</a>'+'<br><span>Location: '+location+'</span></div>';
            var infowindow = new google.maps.InfoWindow();
            google.maps.event.addListener(marker, 'click', (function (marker, content, infowindow) {
                return function () {
                    infowindow.setContent(content);
                    infowindow.open(map, marker);
                };
            })(marker, content, infowindow));
        }
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
</html>
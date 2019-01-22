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
        .modal-content-wrap {
            width: 100%;
            max-width: 600px;
            position: relative;
            margin: 0 auto;
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 100%;
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            font-size: 40px;
            font-weight: bolder;
            transition: 0.3s;
            opacity: 1;
        }

        .close:hover,
        .close:focus {
            color: #fff;
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
    <body>
        <div id="wrapper">

            <?php include 'includes/header.php'; ?>

            <main id="main">

                <?php include 'includes/sidebar.php'; ?>
                <div id="content">
                    <header class="header">
                        <ul class="breadcrumbs list-none">
                            <li  class="colored"><a href="<?= asset('adminlogin') ?>">Dashboard</a></li>

                            <?php if (isset($userEventMembersBreadCrumb) && $userEventMembersBreadCrumb == 1) { ?>
                                <li><a href="<?= asset('users') ?>">Users</a></li>
                                <li><a href="<?= asset('user_events/' . $event->user->id) ?>"><?= $event->user->username ?>'s Events</a></li>
                                <li>Event(<?= $event->title ?>) Members</a></li>
                            <?php } else if (isset($eventMembersBreadCrumb) && $eventMembersBreadCrumb == 1) { ?>
                                <li><a href="<?= asset('events') ?>">Events</a></li>
                                <li>Event(<?= $event->title ?>) Members</a></li>

                            <?php } else if (isset($userEventRsvpsBreadCrumb) && $userEventRsvpsBreadCrumb == 1) { ?>
                                <li><a href="<?= asset('users') ?>">Users</a></li>
                                <li><a href="<?= asset('user_events/' . $event->user->id) ?>"><?= $event->user->username ?>'s Events</a></li>
                                <li>Event(<?= $event->title ?>) Rsvps</a></li>
                            <?php } else if (isset($eventRsvpsBreadCrumb) && $eventRsvpsBreadCrumb == 1) { ?>
                                <li><a href="<?= asset('events') ?>">Events</a></li>
                                <li>Event(<?= $event->title ?>) Rsvps</a></li>

                            <?php } else if (isset($userEventLikedByBreadCrumb) && $userEventLikedByBreadCrumb == 1) { ?>
                                <li><a href="<?= asset('users') ?>">Users</a></li>
                                <li><a href="<?= asset('user_events/' . $event->user->id) ?>"><?= $event->user->username ?>'s Events</a></li>
                                <li>Event(<?= $event->title ?>) Liked By</a></li>
                            <?php } else if (isset($eventLikedByBreadCrumb) && $eventLikedByBreadCrumb == 1) { ?>
                                <li><a href="<?= asset('events') ?>">Events</a></li>
                                <li>Event(<?= $event->title ?>) Liked By</a></li>

                            <?php } else if (isset($userFollowingBreadCrumb) && $userFollowingBreadCrumb == 1) { ?>
                                <li><a href="<?= asset('users') ?>">Users</a></li>
                                <li>User(<?= $user->username ?>) is Following</a></li>
                            <?php } else if (isset($userFollowersBreadCrumb) && $userFollowersBreadCrumb == 1) { ?>
                                <li><a href="<?= asset('users') ?>">Users</a></li>
                                <li>User(<?= $user->username ?>)'s Followers</a></li>
                            <?php } else { ?>
                                <li>Users</li>
                            <?php } ?>
                        </ul>
                        <a href="#" class="btn-sidebar">&#9776;</a>
                    </header>
                    <ul class="achivements list-none">
                        <?php if ((isset($maleBreadCrumb) && $maleBreadCrumb == 1) || (isset($femaleBreadCrumb) && $femaleBreadCrumb == 1) || (isset($facebookBreadCrumb) && $facebookBreadCrumb == 1) || (isset($twitterBreadCrumb) && $twitterBreadCrumb == 1)) { ?>
                            <li>
                                <a class="clickable" href="<?= asset('users') ?>" style="text-decoration: none;">
                                    <div class="txt-holder">
                                        <strong class="counter colored"><?= $allUsers->count(); ?></strong>
                                        <span>Total Users</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="clickable" href="<?= asset('get_users_by_gender/male') ?>" style="text-decoration: none;">
                                    <div class="txt-holder">
                                        <strong class="counter colored"><?= $allUsers->where('gender', 'male')->count(); ?></strong>
                                        <span>Males</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="clickable" href="<?= asset('get_users_by_gender/female') ?>" style="text-decoration: none;">
                                    <div class="txt-holder">
                                        <strong class="counter colored"><?= $allUsers->where('gender', 'female')->count(); ?></strong>
                                        <span>Females</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="clickable" href="<?= asset('get_users_by_login_type/fb') ?>" style="text-decoration: none;">
                                    <div class="txt-holder">
                                        <strong class="counter colored"><?= $allUsers->where('fb_id', '!=', '')->count(); ?></strong>
                                        <span>FB Logins</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="clickable" href="<?= asset('get_users_by_login_type/twitter') ?>" style="text-decoration: none;">
                                    <div class="txt-holder">
                                        <strong class="counter colored"><?= $allUsers->where('twitter_id', '!=', '')->count(); ?></strong>
                                        <span>Twitter Logins</span>
                                    </div>
                                </a>
                            </li>    
                        <?php } else { ?>
                            <li>
                                <a class="clickable" href="<?= asset('users') ?>" style="text-decoration: none;">
                                    <div class="txt-holder">
                                        <strong class="counter colored"><?= $users->count(); ?></strong>
                                        <span>Total Users</span>
                                    </div>
                                </a>
                            </li>
                            <!--                            <li>
                                                            <a class="clickable" href="<?= asset('get_users_by_gender/male') ?>" style="text-decoration: none;">
                                                                <div class="txt-holder">
                                                                    <strong class="counter colored"><?= $users->where('gender', 'male')->count(); ?></strong>
                                                                    <span>Males</span>
                                                                </div>
                                                            </a>
                                                        </li>-->
                            <!--                            <li>
                                                            <a class="clickable" href="<?= asset('get_users_by_gender/female') ?>" style="text-decoration: none;">
                                                                <div class="txt-holder">
                                                                    <strong class="counter colored"><?= $users->where('gender', 'female')->count(); ?></strong>
                                                                    <span>Females</span>
                                                                </div>
                                                            </a>
                                                        </li>-->
                            <li>
                                <a class="clickable" href="<?= asset('get_users_by_login_type/fb') ?>" style="text-decoration: none;">
                                    <div class="txt-holder">
                                        <strong class="counter colored"><?= $users->where('fb_id', '!=', '')->count(); ?></strong>
                                        <span>FB Logins</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="clickable" href="<?= asset('get_users_by_login_type/twitter') ?>" style="text-decoration: none;">
                                    <div class="txt-holder">
                                        <strong class="counter colored"><?= $users->where('twitter_id', '!=', '')->count(); ?></strong>
                                        <span>Twitter Logins</span>
                                    </div>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    <div class="content-area">
                        <div id="map" style="height: 400px;width: 100%"></div>
                        <?php if (Session::has('success')) {
                            ?>
                            <h5 class="alert alert-success" style="position : relative;"><?php echo Session::get('success') ?>
                                <a href="#" class="close" data-dismiss="alert" aria-label="close" style="position :absolute;top :5px;right :10px;">&times</a>
                            </h5>
                            <!--                    <div class="alert alert-success">
                                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                            <?php echo Session::get('success') ?>
                                                </div>-->
                        <?php } ?>
                        <div class="search-area add">
                            <div class="search-query">
                                <!--<strong>Search Users</strong>-->
                                <!--                                <form action="#">
                                                                    <fieldset>
                                                                        <input type="submit" value="submit">
                                                                        <input type="search" placeholder="" id="myInput" onkeyup="myFunction()">
                                                                    </fieldset>
                                                                </form>-->
                            </div>
                        </div>
                        <div class="table-scroll">
                            <table id="myTableUser">
                                <thead>
                                    <tr>
                                        <th>Sr</th>
                                        <th>User Name</th>
                                        <th>Email</th>
                                        <th>Photo</th>
                                        <th>Location</th>
                                        <th>Followers</th>
                                        <th>Followings</th>
                                        <th>Login As</th>
                                        <th>Actions</th>
                                        <th>More Info</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($users as $user) {


                                        $image = asset('public/images/demo.png');
                                        if ($user->photo) {

                                            $image = $user->photo;
                                        }
                                        ?>
                                        <tr user-id="<?= $user->id ?>">
                                            <td><?php
                                                echo $i;
                                                $i++;
                                                ?></td>
                                            <td class="colored"><a class="clickable"><?php echo $user->username ?></a></td>
                                            <td class="colored"><a class="clickable"><?php echo $user->email ?></a></td>
                                            <td class="img-holder"><img style="border-radius: 50%; height: 75px; width: 75px;" user-id="<?= $user->id ?>" src="<?= $image ?>" alt="User Image" class="profile-pic img-responsive"></td>
                                            <td><?php echo $user->location ?></td>
                                            <td><a class="clickable" href="<?php
                                                if ($user->Followers->count() > 0) {
                                                    echo asset('follower/' . $user->id);
                                                } else {
                                                    echo '#';
                                                }
                                                ?>"><?php echo $user->Followers->count() ?></a></td>
                                            <td><a class="clickable" href="<?php
                                                if ($user->Following->count() > 0) {
                                                    echo asset('followings/' . $user->id);
                                                } else {
                                                    echo '#';
                                                }
                                                ?>"><?php echo $user->Following->count() ?></a></td>
                                            <td><?php
                                                if ($user->fb_id) {
                                                    echo 'FB ';
                                                }if ($user->twitter_id) {
                                                    echo 'TW ';
                                                }
                                                ?></td>
                                            <td>
                                                <a href="#user<?= $user->id ?>" class="btn-popup"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                <?php if ($user->is_banned == 1) { ?>
                                                    <a href="#unban<?= $user->id ?>" class="btn-popup"><i class="fa fa-lock" aria-hidden="true"></i></a>
                                                <?php } else { ?>
                                                    <a href="#ban<?= $user->id ?>" class="btn-popup"><i class="fa fa-unlock" aria-hidden="true"></i></a>
                                                <?php } ?>
                                            </td>
                                            <td><a href="<?= asset('user_details/' . $user->id) ?>"><i class="fa fa-info" aria-hidden="true"></i></a>
                                                <?php if ($user->Event->count() > 0) { ?>
                                                    <a href="<?= asset('get_user_events/' . $user->id) ?>">View Events</a>
                                                <?php } ?>

                                            </td>
                                        </tr>

                                    <div id="modal-<?= $user->id ?>" class="modal">
                                        <div class="modal-content-wrap">
                                            <img class="modal-content" id="modal-image-<?= $user->id ?>">
                                            <span class="close">&times;</span>
                                        </div>
                                    </div>

                                    <div id="user<?= $user->id ?>" class="dialogue">
                                        <div class="dialogue-holder">
                                            <div class="confirm-msg">
                                                <div class="confirm-txt">
                                                    <header class="header">
                                                        <h2>Delete User</h2>
                                                        <a href="#" class="btn-close">x</a>
                                                    </header>
                                                    <div class="txt">
                                                        <img src="<?= asset('assets/images/img10.png') ?>" alt="Danger">
                                                        <p>Are you sure you want to delete this User?</p>
                                                        <div class="btns">
                                                            <a href="#" class="btn-primary cancel">Cancel</a>
                                                            <a href="<?= asset('delete_user/' . $user->id) ?>" class="btn-primary delete">Delete User</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="unban<?= $user->id ?>" class="dialogue">
                                        <div class="dialogue-holder">
                                            <div class="confirm-msg">
                                                <div class="confirm-txt">
                                                    <header class="header">
                                                        <h2>Remove User Ban</h2>
                                                        <a href="#" class="btn-close">x</a>
                                                    </header>
                                                    <div class="txt">
                                                        <img src="<?= asset('assets/images/img10.png') ?>" alt="Danger">
                                                        <p>Are you sure you want to unblock this user?</p>
                                                        <div class="btns">
                                                            <a style="text-indent: -30px;" href="#" class="btn-primary cancel">Cancel</a>
                                                            <a style="text-indent: -30px;" href="<?= asset('unban_user/' . $user->id) ?>" class="btn-primary delete">Remove Block</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="ban<?= $user->id ?>" class="dialogue">
                                        <div class="dialogue-holder">
                                            <div class="confirm-msg">
                                                <div class="confirm-txt">
                                                    <header class="header">
                                                        <h2>Block User</h2>
                                                        <a href="#" class="btn-close">x</a>
                                                    </header>
                                                    <div class="txt">
                                                        <img src="<?= asset('assets/images/img10.png') ?>" alt="Danger">
                                                        <p>Are you sure you want to block this User?</p>
                                                        <div class="btns">
                                                            <a href="#" class="btn-primary cancel" style="text-indent: -30px;">Cancel</a>
                                                            <a style="text-indent: -30px;" href="<?= asset('ban_user/' . $user->id) ?>" class="btn-primary delete">Block User</a>
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
    </body>
    <script>
        $(document).ready(function () {
//            $('#myTableUser').DataTable();
<?php if (isset($eventMembersBreadCrumb) && $eventMembersBreadCrumb == 1) { ?>
                $('.menu>li>#events').addClass('active');
<?php } else if (isset($eventRsvpsBreadCrumb) && $eventRsvpsBreadCrumb == 1) { ?>
                $('.menu>li>#events').addClass('active');
<?php } else if (isset($eventLikedByBreadCrumb) && $eventLikedByBreadCrumb == 1) { ?>
                $('.menu>li>#events').addClass('active');
<?php } else { ?>
                $('.menu>li>#users').addClass('active');
<?php } ?>
        });

        $('.clickable').click(function () {
            var userId = $(this).parent('td').parent('tr').attr('user-id');
            window.location.href = '<?php echo asset('user_details/'); ?>/' + userId;
        });

        $('.profile-pic').click(function () {
            var userId = $(this).attr('user-id');
            var modal = document.getElementById('modal-' + userId);
            var modalImg = document.getElementById("modal-image-" + userId);
            modal.style.display = "block";
            modalImg.src = this.src;
        });
        $('.close').click(function () {
            $(this).parents('.modal').css('display', 'none');
        });

        var locations = <?php echo json_encode($users); ?>;
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
                    title: locations[i].username
                });
                markers.push(marker);
                var userId = locations[i].id;
                var username = locations[i].username;
                var photo = locations[i].photo;
                var email = locations[i].email;
                var location = locations[i].location;
                var url = '<?= asset('user_details/') ?>/' + userId;
                var content = '<div class="marker-content-container"><a class="clickable" href="' + url + '"><img class="marker-thumbnail" src=' + photo + '></a><br><a class="clickable" href="' + url + '">' + username + '</a>' + '<br><a class="clickable" href="' + url + '">' + email + '</a>' + '<br><span>' + location + '</span></div>';
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
        $(document).ready(function () {
            var dataTable = $('#myTableUser').DataTable({

            });
            dataTable.on('order.dt search.dt', function () {
                dataTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        });
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7l5RqKcybbGrvSVnI2siEFFuv-VqkuZY&callback=initMap">
    </script>

</html>
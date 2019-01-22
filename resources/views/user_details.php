<!DOCTYPE html>
<html lang="en">

    <?php include 'includes/head.php'; ?>
    <style>
        /* The Modal (background) */
        .image_change {
            text-align: center;
            background-color: #232323;
            border-radius: 50px;
            padding: 10px 20px;
            color: #fff;
            cursor: pointer
        }
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

        /* The Close Button */
        .close {
            position: absolute;
            top: 30;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
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
                            <li class="colored"><a href="<?= asset('adminlogin') ?>">Dashboard</a></li>
                            <li class="colored"><a href="<?= asset('users') ?>">Users</a></li>
                            <li><?= $user->username; ?></li>
                        </ul>
                        <a href="#" class="btn-sidebar">&#9776;</a>
                    </header>
                    <div class="user_data">
                        <form id="changeImage" method="post" action="<?= asset('change_user_image') ?>" enctype="multipart/form-data">
                            <div class="image_user">
                                <?= csrf_field() ?>
                                <figure><img class="profile-pic" src="<?= $user->photo ?>"></figure>
                                <input name="file" onchange="changeImage()" type="file" id="change_image" style="display:none"/>
                                <input name="user_id"  type="hidden" value="<?= $user->id ?>"/>
                                <label for="change_image" class="image_change">Change Image</label>
                            </div>
                        </form>
                        <div class="content-area">
                            <h3>Detail Here</h3>
                            <dl class="user_info">
                                <dt class="colored">Username</dt>
                                <dd><?= $user->username ?></dd>
                                <dt class="colored">Email</dt>
                                <dd><?= $user->email ?></dd>
                                <dt class="colored">Fb Id</dt>
                                <dd><?= $user->fb_id ?></dd>
                                <dt class="colored">Twitter Id</dt>
                                <dd><?= $user->twitter_id ?></dd>
                                <!--                        <dt class="colored">Gender</dt>
                                                        <dd><?= $user->gender ?></dd>-->
                                <dt class="colored">Location</dt>
                                <dd><?= $user->location ?></dd>
                                <dt class="colored">Device</dt>
                                <dd><?= $user->device_type ?></dd>
                                <dt class="colored">Time Zone</dt>
                                <dd><?= $user->time_zone ?></dd>

                                <div class="hoverable">
                                    <a class="clickable" href="<?= asset('user_rsvp_events/' . $user->id) ?>">
                                        <dt  class="colored">RSVP'd to/Events</dt>
                                        <dd> <?= $user->Rsvp->count() ?> </dd>
                                    </a>
                                </div>
                                <div class="hoverable">
                                    <a class="clickable" href="<?= asset('user_liked_events/' . $user->id) ?>">
                                        <dt class="colored">Liked Events</dt>
                                        <dd> <?= $user->Like->count() ?> </dd>
                                    </a>
                                </div>
                                <div class="hoverable">
                                    <a class="clickable"  href="<?= asset('user_comment_events/' . $user->id) ?>">
                                        <dt class="colored">Commented Events</dt>
                                        <dd> <?= $user->Comment->groupBy('event_id')->count() ?></dd>
                                    </a>
                                </div>
                                <div class="hoverable">
                                    <a class="clickable"  href="<?= asset('user_shared_events/' . $user->id) ?>">
                                        <dt class="colored">Shared Events</dt>
                                        <dd> <?= $user->Share->groupBy('event_id')->count() ?></dd>
                                    </a>
                                </div>
                                <div class="hoverable">
                                    <dt class="colored">User Interest</dt>
                                    <dd>
                                        <ul class="list-none">
                                            <?php if ($user->Intrest->count() > 0) {
                                                foreach ($user->Intrest as $intrest) {
                                                    ?>
                                                    <li><?= $intrest->Intrest->title ?></li>

                                                <?php }
                                            } else { ?>
                                                <li>No interest added</li>
<?php } ?>
                                        </ul>
                                    </dd>
                                </div>
                            </dl>

                            <div id="modal" class="modal">
                                <span class="close">&times;</span>
                                <img class="modal-content" id="modal-image">
                            </div>

                        </div>
                    </div>
                </div>
            </main>
        </div>
<?php include 'includes/footer.php'; ?>
    </body>
    <script>
        $(document).ready(function () {
            $('#myTableUser').DataTable();
            $('.menu>li>#users').addClass('active');
        });

        $('.profile-pic').click(function () {
            var modal = document.getElementById('modal');
            var modalImg = document.getElementById("modal-image");
            modal.style.display = "block";
            modalImg.src = this.src;
        });
        $('.close').click(function () {
            $(this).parent('.modal').css('display', 'none');
        });
        function changeImage() {
            $('#changeImage').submit();
        }

    </script>
</html>
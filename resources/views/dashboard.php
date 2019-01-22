<!DOCTYPE html>
<html lang="en">

    <?php include 'includes/head.php'; ?>

<body>
    <div id="wrapper">
        
        <?php include 'includes/header.php'; ?>
        
        <main id="main">
            
             <?php include 'includes/sidebar.php'; ?>
            <div id="content">
                <header class="header">
                    <ul class="breadcrumbs list-none">
                        <li class="colored">Dashboard</li>
                    </ul>
                    <a href="#" class="btn-sidebar">&#9776;</a>
                </header>
                <ul class="achivements list-none">
                    <li>
                        <a class="clickable" href="<?= asset('users')?>">
                            <div class="txt-holder">
                                <strong class="counter colored"><?= $users?></strong>
                                <span >Total Users</span>
                            </div>
                        </a>
                    </li>
<!--                    <li>
                        <a class="clickable" href="<?= asset('get_users_by_gender/male')?>" style="text-decoration: none;">
                            <div class="txt-holder">
                                <strong class="counter colored"><?= $male ?></strong>
                                <span>Males</span>
                            </div>
                        </a>
                    </li>-->
<!--                    <li>
                        <a class="clickable" href="<?= asset('get_users_by_gender/female')?>" style="text-decoration: none;">
                            <div class="txt-holder">
                                <strong class="counter colored"><?= $female ?></strong>
                                <span>Females</span>
                            </div>
                        </a>
                    </li>-->
                    <li>
                        <a class="clickable" href="<?= asset('events')?>">
                            <div class="txt-holder">
                                <strong class="counter colored"><?= $events?></strong>
                                <span>Total Events</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="clickable" id="upcoming-events" style="text-decoration: none;">
                            <div class="txt-holder">
                                <strong class="counter colored"><?= $upcoming ?></strong>
                                <span>Upcoming Events</span>
                            </div>
                        </a>
                    </li>
                    <form style="display: none;" action="<?= asset('get_events') ?>" id="listing_filter_form" method="post">
                        <input name="_token" type="hidden" value="<?= csrf_token() ?>">
                        <input name="listing_filter" type="hidden" value="4">
                    </form>
                </ul>
            </div>
        </main>
    </div>
    <script>
        $('#upcoming-events').click(function(){
            $( "#listing_filter_form" ).submit();
        });
    </script>
</body>
 <?php include 'includes/footer.php'; ?>
</html>
<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/head.php'; ?>

    <body>
        <div id="wrapper">

            <?php include 'includes/header.php'; ?>

            <main id="main">

                <?php include 'includes/sidebar.php'; ?>
                <div id="content">
                    <header class="header border">
                        <ul class="breadcrumbs list-none">
                            <li class="colored"><a href="<?= asset('adminlogin') ?>">Dashboard</a></li>
                            <li>Notifications</li>
                        </ul>
                        <a href="#" class="btn-sidebar">&#9776;</a>
                    </header>
                    <div class="content-area p-details">
                        <div class="slider-cols add">
                            <div class="col">
                                <header class="heading-txt add">
                                    <h2 class="colored">Send Notification</h2>
                                </header>
                                <?php if ($errors->any()) { ?>
                                    <div class="alert alert-danger">
                                        <ul>
                                            <?php foreach ($errors->all() as $error) { ?>
                                                <li><?= $error ?></li>
                                            <?php }
                                            ?>
                                        </ul>
                                    </div>
<?php
}

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
                                <form action="<?= asset('send_notification') ?>" method="post" class="login-form change-pass">
                                    <fieldset>
                                        <div class="input-fields">
                                            
                                            <textarea placeholder="Please Enter Message Max 150 Words" required="" name="message" maxlength="150"></textarea>
                                            <input name="_token" type="hidden" value="<?= csrf_token() ?>">
                                            
                                        </div>
                                        <div class="row">
                                            <input type="submit" value="Send Notification">
                                        </div>
                                        <div class="row">
                                            <!--<p>Login with your existing Hijinnks credentials</p>-->
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <?php include 'includes/footer.php'; ?>
    </body>
    <script>
        $(document).ready(function(){
            $('.menu>li>#notification').addClass('active');
        });
    </script>
</html>
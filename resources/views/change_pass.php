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
                            <li><a href="main.html">Dashboard</a></li>
                            <li>Change Password</li>
                        </ul>
                        <a href="#" class="btn-sidebar">&#9776;</a>
                    </header>
                    <div class="content-area p-details">
                        <div class="slider-cols add">
                            <div class="col">
                                <header class="heading-txt add">
                                    <h2>Change Password</h2>
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
                                <form action="<?= asset('change_password') ?>" method="post" class="login-form change-pass">
                                    <fieldset>
                                        <div class="input-fields">
                                            <input required="" name="current_password" type="password" placeholder="Current Password">
                                            <input name="_token" type="hidden" value="<?= csrf_token() ?>">
                                            <input required="" name="password" type="password" placeholder="New Password">
                                            <input required="" name="password_confirmation" type="password" placeholder="Confirm Password">
                                        </div>
                                        <div class="row">
                                            <input type="submit" value="CHANGE PASSWORD">
                                        </div>
                                        <div class="row">
                                            <p>Login with your existing Hijinnks credentials</p>
                                        </div>
                                    </fieldset>
                                </form>
                                <header class="heading-txt add">
                                    <h2>Change Username</h2>
                                </header>
                                <form action="<?= asset('change_name') ?>" method="post" class="login-form change-pass">
                                    <fieldset>
                                        <div class="input-fields">
                                            <input required="" name="name" type="text" value="<?= Auth::user()->username?>" placeholder="Name">
                                            <input name="_token" type="hidden" value="<?= csrf_token() ?>">
                                          </div>
                                        <div class="row">
                                            <input type="submit" value="CHANGE NAME">
                                        </div>
                                        <div class="row">
                                            <p>Login with your existing Hijinnks credentials</p>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <div id="dialogue" class="dialogue">
                <div class="dialogue-holder">
                    <div class="confirm-msg">
                        <div class="confirm-txt">
                            <header class="header">
                                <h2>Delete Product</h2>
                                <a href="#" class="btn-close">x</a>
                            </header>
                            <div class="txt">
                                <img src="images/img10.png" alt="Danger">
                                <p>Are you sure you want to delete this product?</p>
                                <div class="btns">
                                    <a href="#" class="btn-primary cancel">Cancel</a>
                                    <a href="#" class="btn-primary delete">Delete Product</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
    </body>
    <script>
        $(document).ready(function(){
            $('.menu>li>#change-password').addClass('active');
        });
    </script>
</html>
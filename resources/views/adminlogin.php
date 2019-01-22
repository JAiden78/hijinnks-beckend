<!DOCTYPE html>
<html lang="en">

    <?php include 'includes/head.php'; ?>

    <body class="login-screen">
        <div id="wrapper">
            <div class="login-area">
                <div class="login-holder">
                    <div class="login-form">
                       
                        <form action="<?= asset('adminlogin') ?>" method="post" class="login-form">
                             <?php if (Session::has('error')) {
                            ?>
                            <div class="alert alert-danger">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                <?php echo Session::get('error') ?>
                            </div>
                        <?php } ?>
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <fieldset>
                                <div class="input-fields">
                                    <input name="email" type="email" placeholder="Email">
                                    <input name="password" type="password" placeholder="Password">
                                </div>
                                <div class="row">
                                    <div class="remember-me">
                                        <input style="visibility: hidden"  type="checkbox" id="remember">
                                        <label style="visibility: hidden" for="remember" class="custom-label">Remember me</label>
                                    </div>
                                    <a style="visibility: hidden" href="#">Forgot Your Password?</a>
                                </div>
                                <div class="row" style="margin: 0 0 15px;overflow: hidden;text-align: center;">
                                    <input type="submit" value="SIGN IN" class="btn-submit">
                                </div>
                                <div class="row" style="margin: 0 0 15px;overflow: hidden;text-align: center;">
                                    <p style="text-align: center;">Login with your existing Hijinnks credentials</p>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
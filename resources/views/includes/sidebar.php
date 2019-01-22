<aside id="sidebar">
    <ul class="menu list-none">
        <?php if(Auth::user()->user_type == 1){ ?>
            <li><a id="users" href="<?= asset('users')?>"><i class="fa fa-user" aria-hidden="true"></i> Users</a></li>
            <li><a id="interests" href="<?= asset('interests')?>"><i class="fa fa-tasks" aria-hidden="true"></i> Interests</a></li>
            <li><a id="notification" href="<?= asset('send_notification')?>"><i class="fa fa-bell" aria-hidden="true"></i>Notification</a></li>
        <?php } ?>
        <li><a id="events" href="<?= asset('events')?>"><i class="fa fa-calendar" aria-hidden="true"></i> Events</a></li>
        <li><a id="hijinks-events" href="<?= asset('hijinnks-events')?>"><i class="fa fa-calendar" aria-hidden="true"></i> Hijinnks Events</a></li>
        <li><a id="change-password" href="<?= asset('change_password')?>"><i class="fa fa-key" aria-hidden="true"></i> Account Setting</a></li>
        <li><a id="logout" href="<?= asset('userlogout')?>"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
    </ul>
</aside>
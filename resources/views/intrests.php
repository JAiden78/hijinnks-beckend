<!DOCTYPE html>
<html lang="en">

    <?php include 'includes/head.php'; ?>
    <style>
        /* The Modal (background) */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            align-items: center;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            -webkit-animation-name: fadeIn;
            -webkit-animation-duration: 0.4s;
            animation-name: fadeIn;
            animation-duration: 0.4s
        }

        /* Modal Content */
        .modal-content {
            position: fixed;
            max-width: 700px;
            background-color: #fefefe;
            width: 100%;
            -webkit-animation-name: slideIn;
            -webkit-animation-duration: 0.4s;
            animation-name: slideIn;
            animation-duration: 0.4s;
            left: 0;
            right: 0;
            margin: auto;
        }

        /* The Close Button */
        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            padding: 2px 16px;
            background-color: #232323;
            color: white;
        }

        .modal-body {padding: 2px 16px;}

        .modal-footer {
            padding: 2px 16px;
            background-color: #232323;
            color: white;
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
                            <li>Interest</li>
                        </ul>
                        <a href="#" class="btn-sidebar">&#9776;</a>
                    </header>
                   
                    <div class="content-area">
                        <?php if (Session::has('success')) {
                            ?>
                            <div class="alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                <?php echo Session::get('success') ?>
                            </div>
                        <?php } ?>
                        <div class="search-area add">
                            <div class="search-query">
                                <strong class="colored">Search Interest</strong>
                                <form action="#">
                                    <fieldset>
                                        <input type="submit" value="submit">
                                        <input type="search" placeholder="" id="myInput" onkeyup="myFunction()">
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                        <div class="table-scroll">
                            <a href="#addintrest" class="btn-primary delete btn-popup">Add Interest</a>
                            
                            <div id="addintrest" class="dialogue">
                                        <div class="dialogue-holder">
                                            <div class="confirm-msg">
                                                <div class="confirm-txt">
                                                    <header class="header">
                                                        <h2>Add Interest</h2>
                                                        <a href="#" class="btn-close">x</a>
                                                    </header>
                                                    <div class="txt">
                                                        <form  method="post" action="<?= asset('interests')?>" class="login-form no-border">
                                                            <div class="input-fields">
                                                                <input type="hidden" name="_token" value="<?= csrf_token()?>">
                                                                <input type="text" name="title" placeholder="Title">
                                                            </div>
                                                            <div class="row"><input type="submit" value="Add interest" required=""></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <table id="myTable">
                                <thead>
                                    <tr>
                                        <th>Sr</th>
                                        <th>Title</th>
                                        <th>Event Used</th>
                                        <th>User Used</th>
                                        <th>Total Used</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($intrests as $intrest) {
                                        ?>
                                        <tr>
                                            <td><?php
                                                echo $i;
                                                $i++;
                                                ?></td>
                                            <td><?php echo $intrest->title ?></td>
                                            <td><a href="<?= asset('events_by_interest/'.$intrest->id)?>"><?php echo $intrest->event->count() ?></a></td>
                                            <td><a href="<?= asset('users_by_interest/'.$intrest->id)?>"><?php echo $intrest->user->count() ?></a></td>
                                            <td><?php echo $intrest->event->count() +  $intrest->user->count(); ?></td>
                                            <td>
                                                <a href="#intrest<?= $intrest->id ?>" class="btn-popup"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                
                                            </td>
                                          
                                        </tr>
                                    <div id="intrest<?= $intrest->id ?>" class="dialogue">
                                        <div class="dialogue-holder">
                                            <div class="confirm-msg">
                                                <div class="confirm-txt">
                                                    <header class="header">
                                                        <h2>Delete Interest</h2>
                                                        <a href="#" class="btn-close">x</a>
                                                    </header>
                                                    <div class="txt">
                                                        <img src="<?= asset('assets/images/img10.png') ?>" alt="Danger">
                                                        <p>Are you sure you want to delete this Interest?</p>
                                                        <div class="btns">
                                                            <a href="#" class="btn-primary cancel">Cancel</a>
                                                            <a href="<?= asset('delete_interest/' . $intrest->id) ?>" class="btn-primary delete">Delete Interest</a>
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
        $(document).ready(function(){
            $('.menu>li>#interests').addClass('active');
        });
    </script>
</html>
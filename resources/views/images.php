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
                            <li><a href="<?= asset('adminlogin') ?>">Dashboard</a></li>
                            <li>Attachments</li>
                        </ul>
                        <a href="#" class="btn-sidebar">&#9776;</a>
                    </header>

                    <div class="content-area">
                        <div class="custom_images">
                            <h2>Images</h2>
                            <ul class="list-none">
                                <?php foreach ($attachments as $attachment) {
                                    if($attachment->type=='image'){ ?>
                                <li><img src="<?=  asset('public/images/events/'.$attachment->attachment_path)?>"></li>
                                <?php }} ?>
                            
                            </ul>
                        </div>
                        <div class="custom_images">
                            <h2>Videos</h2>
                            <ul class="list-none">
                                <?php foreach ($attachments as $attachment) {
                                    if($attachment->type=='video'){ ?>
                                <li><video width="320" height="240" controls>
                                        <source src="<?=  asset('public/videos/events/'.$attachment->attachment_path)?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video></li>
                                      <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <?php include 'includes/footer.php'; ?>
    </body>

</html>
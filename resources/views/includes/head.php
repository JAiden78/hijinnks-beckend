<head>
    <title><?= $title?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?=csrf_token()?>">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link href="<?= asset('assets/css/bootstrap-datetimepicker.min.css')?>" media="all" rel="stylesheet">
    
<!--    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">-->
<!--    <link rel="stylesheet" type="text/css" media="screen"-->
<!--          href="http://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">-->


    <link href="<?= asset('assets/css/font-awesome.min.css')?>" media="all" rel="stylesheet">
    <link href="<?= asset('assets/css/all.css')?>" media="all" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Karla:400,400i,700,700i" rel="stylesheet">
    <link href="<?= asset('assets/css/jquery.dataTables.min.css')?>" media="all" rel="stylesheet">



    <link href="http://hayageek.github.io/jQuery-Upload-File/4.0.11/uploadfile.css" rel="stylesheet">
<!--    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://hayageek.github.io/jQuery-Upload-File/4.0.11/jquery.uploadfile.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!--    <script type="text/javascript"-->
<!--            src="http://tarruda.github.com/bootstrap-datetimepicker/assets/js/bootstrap-datetimepicker.min.js">-->
<!--    </script>-->
<!--    <script type="text/javascript"-->
<!--            src="http://tarruda.github.com/bootstrap-datetimepicker/assets/js/bootstrap-datetimepicker.pt-BR.js">-->
<!--    </script>-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.17/moment-timezone-with-data.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.6/jstz.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datejs/1.0/date.min.js"></script>

    <script src="<?= asset('assets/js/bootstrap-datetimepicker.min.js')?>"></script>

    <script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places&key=AIzaSyAlzOSZjP-VkWkYWl6MERVHK-xwyh_7qys"></script>

    <script src="<?= asset('assets/js/jquery.geocomplete.min.js')?>"></script>

    <link rel="shortcut icon" type="image/png" href="<?= asset('assets/images/favicon.png')?>"/>


    <!-- Add fancyBox main JS and CSS files -->
    <script type="text/javascript" src="<?=asset('assets/fancybox/jquery.fancybox.pack.js?v=2.1.5');?>"></script>
    <link rel="stylesheet" type="text/css" href="<?=asset('assets/fancybox/jquery.fancybox.css?v=2.1.5');?>" media="screen" />

    <!-- Add Button helper (this is optional) -->
    <link rel="stylesheet" type="text/css" href="<?=asset('assets/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5');?>"/>
    <script type="text/javascript" src="<?=asset('assets/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5');?>"></script>

    <!-- Add Thumbnail helper (this is optional) -->
    <link rel="stylesheet" type="text/css" href="<?=asset('assets/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7');?>"/>

    <link rel="stylesheet" type="text/css" href="<?=asset('assets/select2/css/select2.min.css');?>"/>

    <script type="text/javascript" src="<?=asset('assets/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7');?>"></script>

    <!-- Add Media helper (this is optional) -->
    <script type="text/javascript" src="<?=asset('assets/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6');?>"></script>


</head>
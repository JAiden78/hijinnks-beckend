<?php

$video_directory = "../../../public/videos/events/";
$image_directory = "../../../public/images/events/";
$video_extensions = array('mp4');
$image_extensions = array('png', 'jpg', 'jpeg', 'bmp');
if(isset($_FILES["myfile"]))
{
//    $ret = array();

//	This is for custom errors;
    /*	$custom_error= array();
        $custom_error['jquery-upload-file-error']="File already exists";
        echo json_encode($custom_error);
        die();
    */
    $error =$_FILES["myfile"]["error"];
    //You need to handle  both cases
    //If Any browser does not support serializing of multiple files using FormData()
    if(!is_array($_FILES["myfile"]["name"])) //single file
    {
        $fileName = $_FILES["myfile"]["name"];
        $tmp = explode('.', $fileName);
        $end = strtolower(end($tmp));
        $file_extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(in_array($end, $image_extensions)){
            $fileName = 'eventimage_' . generateRandomString() . '.' . $file_extension;
            move_uploaded_file($_FILES["myfile"]["tmp_name"],$image_directory.$fileName);
            $ret= $fileName;
        }
        else if(in_array($end, $video_extensions)){
            $fileName = 'eventvideo_' . generateRandomString() . '.' . $file_extension;
            move_uploaded_file($_FILES["myfile"]["tmp_name"],$video_directory.$fileName);
            $ret= $fileName;
        }
    }
    else  //Multiple files, file[]
    {
        $fileCount = count($_FILES["myfile"]["name"]);
        for($i=0; $i < $fileCount; $i++)
        {
            $fileName = $_FILES["myfile"]["name"][$i];
            $tmp = explode('.', $fileName);
            $end = strtolower(end($tmp));
            $file_extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if(in_array($end, $image_extensions)) {
                $fileName = 'eventimage_' . generateRandomString() . '.' . $file_extension;
                move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $image_directory.$fileName);
                $ret = $fileName;
            }
            else if(in_array($end, $video_extensions)) {
                $fileName = 'eventvideo_' . generateRandomString() . '.' . $file_extension;
                move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $video_directory.$fileName);
                $ret = $fileName;
            }
        }
    }
    echo $ret;
}
function generateRandomString($length = 15) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>
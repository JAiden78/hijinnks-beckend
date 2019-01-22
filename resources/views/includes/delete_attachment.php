<?php
$video_directory = "../../../public/videos/events/";
$image_directory = "../../../public/images/events/";
$video_extensions = array('mp4');
$image_extensions = array('png', 'jpg', 'jpeg', 'bmp');
$filePath = null;
if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
{
	$fileName =$_POST['name'];
    $file_extension = pathinfo($fileName, PATHINFO_EXTENSION);

    $tmp = explode('.', $fileName);
    $end = end($tmp);

    if(in_array($end, $image_extensions)){
        $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
        $filePath = $image_directory. $fileName;
    }
    else if(in_array($end, $video_extensions)){
        $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
        $filePath = $video_directory. $fileName;
    }
    if($filePath != null){
        if (file_exists($filePath))
        {
            unlink($filePath);
        }
        echo "Deleted File ".$fileName."<br>";
    }
}
?>
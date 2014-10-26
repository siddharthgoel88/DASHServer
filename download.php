<?php

$qosArray = array(64, 128, 256, 512);
$dir = "upload/";

//format: http://pilatus.d1.comp.nus.edu.sg/~a0039874/download.php?video=seafood&speed=256&segment=1
function downloadFile($file) { // $file = include path 
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    } else {
        return 0;
    }
}

//var_dump($_GET);die();
$videoName = $_GET['video'];
$segment = $_GET['segment'];
$bitrate = $_GET['speed'];
if (!downloadFile($dir . $videoName . "/" . $segment . "_" . $bitrate . ".m3u8")) {
    //error handling for non-existing quality
    if (!in_array($bitrate, $qosArray)) {
        echo '<br>Bitrate of ' . $bitrate . ' does not exists.<br>';
        foreach($qosArray as $index=>$item){
            if($item>$bitrate){
                echo '<br>The Closest Bit Rate (smaller than given) is '.$qosArray[($index-1)>0?($index-1):0].'<br>';
                break;
            }
        }
    }
    echo '<br>cannot find the file<br>';
}
?>
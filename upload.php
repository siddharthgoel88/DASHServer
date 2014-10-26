<?php

$qosArray = array(64, 128, 256, 512);
$dir = "upload/";

function getname($name) {
    $dir = "upload/";

    if (!is_dir($dir)) {
        echo "this is not a dir for " . $dir;
        if (mkdir($dir, 0777)) {
            echo "mkdir success!<br>";
        } else {
            echo "mkdir failed!<br>";
        }
    }

//    system('chcon -R -t httpd_sys_rw_content_t ' . $dir);
    return $dir . $name;
}

$uploadfile = getname($_FILES['uploaded']['name']);

system("echo '$uploadfile' > upload/test.txt");

if (move_uploaded_file($_FILES['uploaded']['tmp_name'], $uploadfile)) {
    echo "<h2><font color=#00ff00>Success!</font></h2><br><br>";
} else {
    echo "<h2><font color=#ff0000>Failed!</font></h2><br><br>";
}


echo "File Info:
    <br><br>File Name:" . $_FILES['uploaded']['name'] .
 "<br><br>Type:" . $_FILES['uploaded']['type'] .
 "<br><br>Temp File Name:" . $_FILES['uploaded']['tmp_name'] .
 "<br><br>Size:" . $_FILES['uploaded']['size'] / 1024 . "K" .
 "<br><br>Error:" . $_FILES['uploaded']['error'];

//convert video quality and save
$name = strtolower(substr($_FILES['uploaded']['name'], 0, (strpos($_FILES['uploaded']['name'], '.'))));

if (mkdir($dir . $name, 0777)) {
    foreach ($qosArray as $bitrate) {
        system('/user/local/bin/convert.sh ' . $_FILES['uploaded']['tmp_name'] . ' ' . $bitrate . ' 29.97 320x240 44100 64 ' . $dir . $name . "/" . $bitrate . '.m3u8');
    }
} else {
    echo "<br>cannot mkdir for the video.<br>";
}
?>

<?php

$segmentNumber=1;
$videoname="";
$videodir="";
$videopath="";
$fullname="";

if(file_exists("logs/logs.txt"))
        $logfp = fopen("logs/logs.txt", "a");
else
        $logfp = fopen("logs/logs.txt", "w");


/*
Convinent method to facilitate debugging
*/
function debug($msg)
{
	global $logfp;
	$msg = "upload.php ===> " .$msg . "\n";
	echo $msg;
	fwrite($logfp, $msg);
}

/*
Parsing the name of the file uploaded and extract details
from the name. 
*/
function parseName($name)
{
	global $segmentNumber,$videoname;
	preg_match("/^(.+)---(\d+).mp4$/",$name, $result);
	$videoname = $result[1];
	$segmentNumber = $result[2];
}

/*
Method that transcodes the segments uploaded to 
240*160 and 480*320 resolutions in .mp4 and .ts formats.
*/
function transcode()
{
	global $videoname, $videopath, $segmentNumber;
	$lowdir = "upload/$videoname/low";
	$lowvideoname = "$lowdir/$videoname-240*160---$segmentNumber.mp4";
	$middir = "upload/$videoname/mid";
	$midvideoname = "$middir/$videoname-480*320---$segmentNumber.mp4";
	$bitratelow = "200";
	$bitratemid = "768";
	$fps = "29.97";
	$lowres = "240x160";
	$midres = "480x320";
	$asps = "44100";
	$audiobitrate = "64";

	$lowts = "$lowdir/$videoname-240*160---$segmentNumber.ts";
	$midts = "$middir/$videoname-480*320---$segmentNumber.ts";

	if(!file_exists($lowdir))
		mkdir($lowdir, 0777, true);
	
	if(!file_exists($middir))
		mkdir($middir, 0777, true);

	$cmd1 = "/usr/local/bin/convert.sh $videopath $bitratelow $fps $lowres $asps $audiobitrate $lowvideoname";
	$cmd2 = "/usr/local/bin/convert.sh $videopath $bitratemid $fps $midres $asps $audiobitrate $midvideoname";
	$cmd3 = "/usr/local/bin/mp42ts $lowvideoname $lowts";
	$cmd4 = "/usr/local/bin/mp42ts $midvideoname $midts";
	
	//Execution of command for transcoding and generation of .ts format video

	debug("Executing $cmd1");
	system($cmd1);
	debug("Low transcoding for $videopath complete");

	debug("Executing $cmd2");
	system($cmd2);
	debug("Medium transcoding for $videopath complete");
	
	debug("Executing $cmd3");
	system($cmd3);
	debug("Created $lowts from $lowvideoname");
	
	debug("Executing $cmd4");
	system($cmd4);
	debug("Created $midts from $midvideoname");

}


/*
Logic handling upload of the streamlets
*/
if (($_FILES["uploaded"]["size"] < 20000000))
  {
  if ($_FILES["uploaded"]["error"] > 0)
    {
	$error = "Return Code: " . $_FILES["uploaded"]["error"];
	debug($error);
    }
  else
    {
	debug("Upload: " . $_FILES["uploaded"]["name"]);
	debug("Type: " . $_FILES["uploaded"]["type"]);
	debug("Size: " . ($_FILES["uploaded"]["size"] / 1024) . " Kb");
	debug("Temp file: " . $_FILES["uploaded"]["tmp_name"]);

	$fullname = $_FILES["uploaded"]["name"];
	parseName($fullname);
	$videodir = "upload/" . $videoname . "/high";
	$videopath = "$videodir/$videoname-720*480---$segmentNumber.mp4";

	if(!file_exists($videodir))
	{
		mkdir($videodir, 0777, true);
	}

	move_uploaded_file($_FILES["uploaded"]["tmp_name"], $videopath);
	debug("Stored in: $videopath");

	$tspath = "$videodir/$videoname-720*480---$segmentNumber.ts";
	system("/usr/local/bin/mp42ts $videopath $tspath");

	transcode();
#      }
    }
  }
else
  {
  echo "Invalid file";
  }

	fclose($logfp);
	exit;
?>

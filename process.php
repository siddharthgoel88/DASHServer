<?php

$foldername = $_POST["foldername"];
#$foldername = "DASH_Video_01_11_2014_10_20_00";
$videopath = "upload/" . $foldername . "/high/";

/*
if(file_exists("logs/logs.txt"))
	$logfp = fopen("logs/logs.txt", "a");
else
	$logfp = fopen("logs/logs.txt", "w");
*/

$basicxml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<MPD xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xmlns="urn:mpeg:mpegB:schema:DASH:MPD:DIS2011"
     xsi:schemaLocation="urn:mpeg:mpegB:schema:DASH:MPD:DIS2011"
     profiles="urn:mpeg:mpegB:profile:dash:full:2011"
     minBufferTime="PT2.0S">
     <BaseURL>http://pilatus.d1.comp.nus.edu.sg/~a0110280/upload/</BaseURL>
     <Period start="PT0S">
          <Group mimeType="video/mp4">
              <Representation width="720" height="480" id="high" bandwidth="3000000">
                  <SegmentInfo duration="PT03.00S">
                  </SegmentInfo>
              </Representation>
              <Representation width="480" height="320" id="medium" bandwidth="768000">
                  <SegmentInfo duration="PT03.00S">
                  </SegmentInfo>
              </Representation>
              <Representation width="240" height="160" id="low" bandwidth="200000">
                  <SegmentInfo duration="PT03.00S">
                  </SegmentInfo>
              </Representation>
          </Group>
    </Period>
</MPD>
XML;

$playlistxml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<playlist>
</playlist>
XML;

/*
function debug($msg)
{
	global $logfp;
	$msg = "process.php ===> " . $msg;
	echo $msg;
	fwrite($logfp, $msg);
}
*/

#Function to add element to mpd file
function addToMPD($mp4count)
{
	echo "addToMPD\n";
	global $mpd, $foldername;
	$high = $mpd->Period->Group->Representation[0]->SegmentInfo; 
	$mid = $mpd->Period->Group->Representation[1]->SegmentInfo; 
	$low = $mpd->Period->Group->Representation[2]->SegmentInfo; 
	
	for ($i=1; $i<=$mp4count; $i++)
	{
		$highChild = $high->addChild("Url");
		$highChild->addAttribute("sourceUrl" , $foldername . "/" . $foldername . "-720*480---" . $i . ".mp4");

		$midChild = $mid->addChild("Url");
		$midChild->addAttribute("sourceUrl" , $foldername . "/" . $foldername . "-480*320---" . $i . ".mp4");
		
		$lowChild = $low->addChild("Url");
		$lowChild->addAttribute("sourceUrl" , $foldername . "/" . $foldername . "-240*160---" . $i . ".mp4");

	}
	echo "end";
}


function addToPlaylist($mpdFilePath)
{
	echo "123";
	global $baseurl, $playlistxml;
	echo "q23";
	$attr = $baseurl . $mpdFilePath;
	echo "w23";
	$playlistpath = "upload/playlist.xml";

	echo "e23";
	if(file_exists($playlistpath))
	{
		echo "fi43423";
		$playlist = simplexml_load_file($playlistpath);
		echo "d3423";
	}
	else
	{
		try {
		echo "z23";
		$playlist = new SimpleXMLElement($playlistxml);
		echo "rhf23";
		} catch(Exception $e) {
			echo "In exception";
			echo $e->getMessage();
		}
	}	
	
	echo "f23";
	$mpdchild = $playlist->addChild("mpd");
	echo "d23";
	$mpdchild->addAttribute("path", $attr);
	
	echo "s23";
	$fp = fopen($playlistpath, "w") or die("Unable to open playlist file");
	echo "g23";
	fwrite($fp, formatXML($playlist));
	echo "t23";
	fclose($fp);
	echo "end2";
}


function formatXML($oldxml)
{
	echo "for12";
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($oldxml->asXML());
	echo $dom->saveXML();
	return $dom->saveXML();
}

function createM3U8($mp4count)
{
	global $foldername,$baseurl;
	$lowts = fopen("upload/$foldername/low/low.m3u8", "w") or die("Unable to create m3u8 file");
	$midts = fopen("upload/$foldername/mid/mid.m3u8", "w") or die("Unable to create m3u8 file");
	$hights = fopen("upload/$foldername/high/high.m3u8", "w") or die("Unable to create m3u8 file");
	$rootts = fopen("upload/$foldername/root.m3u8", "w") or die("Unable to create m3u8 file");

	fwrite($lowts, "#EXTM3U\n#EXT-X-TARGETDURATION:3\n");
	fwrite($midts, "#EXTM3U\n#EXT-X-TARGETDURATION:3\n");
	fwrite($hights, "#EXTM3U\n#EXT-X-TARGETDURATION:3\n");
	fwrite($rootts, "#EXTM3U\n");
	
	for ($i=1; $i<=$mp4count; $i++)
	{
		fwrite($lowts, "#EXTINF:3,\n$foldername-240*160---$i.ts\n");
		fwrite($midts, "#EXTINF:3,\n$foldername-480*320---$i.ts\n");
		fwrite($hights, "#EXTINF:3,\n$foldername-720*480---$i.ts\n");
	}

	fwrite($lowts, "#EXT-X-ENDLIST");
	fwrite($midts, "#EXT-X-ENDLIST");
	fwrite($hights, "#EXT-X-ENDLIST");

	fwrite($rootts, "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=200000,RESOLUTION=240x160\n");
	fwrite($rootts, "low/low.m3u8\n");
	fwrite($rootts, "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=768000,RESOLUTION=480x320\n");
	fwrite($rootts, "mid/mid.m3u8\n");
	fwrite($rootts, "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=3000000,RESOLUTION=720x480\n");
	fwrite($rootts, "high/high.m3u8\n");

	fclose($lowts);
	fclose($midts);
	fclose($hights);

}

$mpd = new SimpleXMLElement($basicxml);
$baseurl = $mpd->BaseURL;
$mp4files = glob($videopath . '*.mp4');
echo "Video path is " . $videopath . "\n";

if($mp4files !== false)
{
	$mp4count = count( $mp4files );
	#echo "Number of files=".$mp4count;
	addToMPD($mp4count);
	createM3U8($mp4count);
}

#$mpdFilePath = "upload/" . $foldername . "/" . $foldername . ".mpd";
$mpdFilePath = "upload/$foldername/$foldername.mpd";
echo $mpdFilePath;

$mpdfp = fopen($mpdFilePath, "w") or die("Unable to open file");
fwrite($mpdfp, formatXML($mpd));
fclose($mpdfp);

$mpdPath = "$foldername/$foldername.mpd";
addToPlaylist($mpdPath);



//fclose($logfp);
?>

<?php

$foldername = $_POST["foldername"];
#$foldername = "DASH_Video_01_11_2014_10_20_00";
$videopath = "upload/" . $foldername . "/high/";

$basicxml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<MPD xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xmlns="urn:mpeg:mpegB:schema:DASH:MPD:DIS2011"
     xsi:schemaLocation="urn:mpeg:mpegB:schema:DASH:MPD:DIS2011"
     profiles="urn:mpeg:mpegB:profile:dash:full:2011"
     minBufferTime="PT2.0S">
     <BaseURL>http://pilatus.d1.comp.nus.edu.sg/~a0040609/upload/</BaseURL>
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

#Function to add element to mpd file
function addToMPD($mp4count)
{
	global $mpd, $foldername;
	$high = $mpd->Period->Group->Representation[0]->SegmentInfo; 
	$mid = $mpd->Period->Group->Representation[1]->SegmentInfo; 
	$low = $mpd->Period->Group->Representation[2]->SegmentInfo; 
	
	for ($i=1; $i<=$mp4count; $i++)
	{
		$highChild = $high->addChild("Url");
		$highChild->addAttribute("sourceUrl" , $foldername . "-720*480---" . $i . ".mp4");

		$midChild = $mid->addChild("Url");
		$midChild->addAttribute("sourceUrl" , $foldername . "-480*320---" . $i . ".mp4");
		
		$lowChild = $low->addChild("Url");
		$lowChild->addAttribute("sourceUrl" , $foldername . "-240*160---" . $i . ".mp4");

	}
}


$mpd = new SimpleXMLElement($basicxml);
$mp4files = glob($videopath . '*.mp4');
echo "Video path is " . $videopath . "\n";

if($mp4files !== false)
{
	$mp4count = count( $mp4files );
	#echo "Number of files=".$mp4count;
	addToMPD($mp4count);
}

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($mpd->asXML());
echo $dom->saveXML();

#$mpdFilePath = "upload/" . $foldername . "/" . $foldername . ".mpd";
$mpdFilePath = "upload/$foldername/$foldername.mpd";
echo $mpdFilePath;
$mpdfp = fopen($mpdFilePath, "w") or die("Unable to open file");
fwrite($mpdfp, $dom ->saveXML());
fclose($mpdfp);

?>

<?php
//origin checks for trusted sites...
if (isset($_SERVER["HTTP_ORIGIN"]) === true) {
	$origin = $_SERVER["HTTP_ORIGIN"];
	
	if(strcmp($origin,"https://www.jbryanmarcus.com")&&strcmp($origin,"https://www.copelandwebdesign.com")){
		echo "bad origin";
		exit;
	}
	header('Access-Control-Allow-Origin: ' . $origin);
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
		exit; 
	}
}
//if not numeric then run away screaming.
//Go style structure here where I check for failure cases first, I most have wrote this part later once I started learning go...
if(!is_numeric($_REQUEST['i']))
	exit;
$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","-------");
$res = mysqli_query($con,"select * from marcmusicplayer.TrackInfo where id=".$_REQUEST['i'].";");
//Get the url for the audio file...
$filename=$res->fetch_array()['audiourl'];
if(strpos($filename,"copelandwebdesign.com")){
	$parts=explode("/",$filename);
	$filename=$parts[5]."/".$parts[6];
	echo $filename;
	//Wipe that shit
	if(!unlink($filename)){
		$file=fopen("Audio/log.txt","a");
		if($file){
			printf("$filename was unable to be deleted due to an error!");
			fclose($file);
		}
	}
}
//And remove all traces of Records and Track info
$res = mysqli_query($con,"delete from marcmusicplayer.Music where trackid=".$_REQUEST['i'].";");
$res = mysqli_query($con,"delete from marcmusicplayer.TrackInfo where id=".$_REQUEST['i'].";");	
mysqli_close($con);
?>

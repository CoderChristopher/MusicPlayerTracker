<?php
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
if(!is_numeric($_REQUEST['i']))
	exit;
$con = mysqli_connect("mysql.copelandwebdesign.com","copeland","5z2ojgwB!");
$res = mysqli_query($con,"select * from marcmusicplayer.TrackInfo where id=".$_REQUEST['i'].";");
$filename=$res->fetch_array()['audiourl'];
if(strpos($filename,"copelandwebdesign.com")){
	$parts=explode("/",$filename);
	$filename=$parts[5]."/".$parts[6];
	echo $filename;
	if(!unlink($filename)){
		$file=fopen("Audio/log.txt","a");
		if($file){
			printf("$filename was unable to be deleted due to an error!");
			fclose($file);
		}
	}
}
$res = mysqli_query($con,"delete from marcmusicplayer.Music where trackid=".$_REQUEST['i'].";");
$res = mysqli_query($con,"delete from marcmusicplayer.TrackInfo where id=".$_REQUEST['i'].";");	
mysqli_close($con);
?>

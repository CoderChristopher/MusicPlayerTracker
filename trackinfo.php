<?php
if (isset($_SERVER["HTTP_ORIGIN"]) === true) {
	$origin = $_SERVER["HTTP_ORIGIN"];
	
	if(strcmp($origin,"https://www.jbryanmarcus.com")&&strcmp($origin,"https://www.copelandwebdesign.com"))
		exit;
	header('Access-Control-Allow-Origin: ' . $origin);
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
		exit; 
	}
}
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS"&&!is_numeric($_REQUEST['i'])){
	exit; 
}
$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","--------");
if(isset($_REQUEST['i']))
	$res = mysqli_query($con,"select * from marcmusicplayer.TrackInfo where id=".$_REQUEST['i'].";");	
else
	$res = mysqli_query($con,"select * from marcmusicplayer.TrackInfo;");	
while(($line=$res->fetch_object()))
	echo $line->trackname.";".$line->length.";".$line->audiourl.";;".$line->id.";";
mysqli_close($con);
?>

<?php
//Check origin for trusted sites...
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
//q variable must be a stub left form when I was going to implment functionlity for selecting the index of the search
if(isset($_REQUEST['q'])&&is_numeric($_REQUEST['q'])){
	$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","--------");
	$res = mysqli_query($con,"select * from marcmusicplayer.Music;");	
	while(($line=$res->fetch_object()))
		echo $line->trackid.":".$line->utm.":".$line->time.":".$line->date.":";
	mysqli_close($con);
}else
	exit;
?>

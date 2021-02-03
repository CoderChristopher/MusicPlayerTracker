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
$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","-------");
$res = mysqli_query($con,"delete from marcmusicplayer.Music where id=".$_REQUEST['i'].";");
mysqli_close($con);
?>

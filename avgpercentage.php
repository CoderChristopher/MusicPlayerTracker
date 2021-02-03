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
if(isset($_REQUEST['s'])&&is_numeric($_REQUEST['s'])){
	$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","----------");
	$res = mysqli_query($con,"select avg(time) from marcmusicplayer.Music where time>0 and trackid=\"".$_REQUEST['s']."\";");	
	echo $res->fetch_array()[0];
	mysqli_close($con);
}else
	exit;
?>

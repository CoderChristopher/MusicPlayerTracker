<?php
//Do some origin checking to make see if the origin of a request is from a trusted source
//and is in a expected format
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
//s stands for track id to be referenced. With SQL injection being a concern I do my best
//to limit the use of straight text input into a SQL statement. The use of is_numeric here
//is intended to be very stringent sanitation of input do prevent the possibility of such
//attacks
if(isset($_REQUEST['s'])&&is_numeric($_REQUEST['s'])){
	$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","----------");
	$res = mysqli_query($con,"select avg(time) from marcmusicplayer.Music where time>0 and trackid=\"".$_REQUEST['s']."\";");	
	echo $res->fetch_array()[0];
	mysqli_close($con);
}else
	exit;
?>

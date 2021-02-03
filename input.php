<?php
$start=microtime();
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
if(!isset($_REQUEST['id'])&&!is_numeric($_REQUEST['time'])&&!isset($_REQUEST['time'])&&!is_numeric($_REQUEST['time'])&&strpos($_REQUEST['utm'],"'")!==false&&strpos($_REQUESTT['utm'],"\"")!==false){
	echo "Appropriate request information not set!";
	exit;
}
$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","-------");
if(mysqli_connect_errno()){
	echo "There was an error when connecting.</br></br>".mysqli_connect_error();
	exit;
}
if(!isset($_REQUEST['trackid'])||!isset($_REQUEST['id'])){
	echo "Info not set!</br>";
	exit;
}
echo "Connection successfully established!</br>";
$res = mysqli_query($con, "select * from marcmusicplayer.Music where id=\"".$_REQUEST['id']."\";");
if($res->num_rows==0){
	$res = mysqli_query($con,"insert into marcmusicplayer.Music (id,time,utm,trackid,date) values (\"".$_REQUEST['id']."\",".$_REQUEST['time'].",\"".$_REQUEST['utm']."\",".$_REQUEST['trackid'].",".time().");");
	echo "insert</br>";
}else{
	$res = mysqli_query($con,"update marcmusicplayer.Music set time=".$_REQUEST['time']." where id=\"".$_REQUEST['id']."\";");
	echo "update</br>";
}
mysqli_close($con);
echo "Runtime:".(microtime()-$start)."</br>";
?>

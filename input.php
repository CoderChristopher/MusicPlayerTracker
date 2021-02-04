<?php

$start=microtime();
//origin checking...
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
//do some check checks for sql injection attempts, malformed data, etc.
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
//Do a select check. If the select turns up no rows then must mean this is the first time a request has been done with this
//unique session id. So therefore a new record must be generated with a insert statement.
//Otherwise just go and update the record using the given session id
$res = mysqli_query($con, "select * from marcmusicplayer.Music where id=\"".$_REQUEST['id']."\";");
if($res->num_rows==0){
	$res = mysqli_query($con,"insert into marcmusicplayer.Music (id,time,utm,trackid,date) values (\"".$_REQUEST['id']."\",".$_REQUEST['time'].",\"".$_REQUEST['utm']."\",".$_REQUEST['trackid'].",".time().");");
	echo "insert</br>";
}else{
	$res = mysqli_query($con,"update marcmusicplayer.Music set time=".$_REQUEST['time']." where id=\"".$_REQUEST['id']."\";");
	echo "update</br>";
}
mysqli_close($con);
//This is really just a simple timer I was using to track how long it took for this function to run when I was looking for bottle necks
echo "Runtime:".(microtime()-$start)."</br>";
?>

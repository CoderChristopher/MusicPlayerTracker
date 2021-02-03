<?php
/*if(strcmp($_SERVER['HTTP_ORIGIN'],"https://www.copelandwebdesign.com")!==0){
	echo "bad origin:".$_SERVER['HTTP_ORIGIN'];
	exit;
}*/
if(isset($_REQUEST['s'])&&is_numeric($_REQUEST['s'])){
	$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","-------");
	$res = mysqli_query($con,"select sum(time) from marcmusicplayer.Music where trackid=\"".$_REQUEST['s']."\";");	
	echo $res->fetch_array()[0];
	mysqli_close($con);
}else
	exit;
?>
